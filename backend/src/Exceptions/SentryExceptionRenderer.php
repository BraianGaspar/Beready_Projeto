<?php
declare(strict_types=1);

namespace App\Exceptions;

use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Error\ExceptionRendererInterface;
use Cake\Http\Exception\HttpException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class SentryExceptionRenderer implements ExceptionRendererInterface
{
    private Throwable $error;
    private ?ServerRequest $request = null;

    public function __construct(Throwable $error, ?ServerRequest $request = null)
    {
        $this->error = $error;
        $this->request = $request;
    }

    public function render(): Response
    {
        $exception = $this->error;
        $status = $this->statusFor($exception);

        // Erros de cliente (4xx) são esperados; só os 5xx vão para o Sentry
        if ($status >= 500) {
            try {
                \Sentry\captureException($exception);
                \Sentry\flush(2000);
            } catch (\Throwable $e) {
                Log::error('Sentry: ' . $e->getMessage());
            }
        }

        $debug = (bool)Configure::read('debug');

        $responseData = [
            'success' => false,
            'message' => $status >= 500 && !$debug ? 'Erro interno do servidor' : $exception->getMessage(),
        ];

        if ($debug) {
            $responseData['debug'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'type' => get_class($exception)
            ];
        }

        return (new Response())
            ->withStatus($status)
            ->withType('application/json')
            ->withStringBody(json_encode($responseData));
    }

    private function statusFor(Throwable $exception): int
    {
        if ($exception instanceof HttpException) {
            return $this->validStatus($exception->getCode());
        }

        return match (true) {
            $exception instanceof EmailAlreadyExistsException => 409,
            $exception instanceof WeakPasswordException,
            $exception instanceof InvalidTokenException,
            $exception instanceof \InvalidArgumentException => 400,
            $exception instanceof UserNotFoundException,
            $exception instanceof FlashcardNotFoundException,
            $exception instanceof QuizNotFoundException,
            $exception instanceof RecordNotFoundException => 404,
            $exception instanceof CakeException => $this->validStatus($exception->getCode()),
            $exception instanceof \RuntimeException && $exception->getCode() === 404 => 404,
            default => 500,
        };
    }

    private function validStatus(mixed $code): int
    {
        return is_int($code) && $code >= 400 && $code < 600 ? $code : 500;
    }

    public function write(ResponseInterface|string $output): void
    {
        if (is_string($output)) {
            $output = (new Response())->withStringBody($output);
        }

        foreach ($output->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        $statusLine = sprintf(
            'HTTP/%s %d %s',
            $output->getProtocolVersion(),
            $output->getStatusCode(),
            $output->getReasonPhrase()
        );
        header($statusLine, true, $output->getStatusCode());

        echo (string)$output->getBody();
    }
}
