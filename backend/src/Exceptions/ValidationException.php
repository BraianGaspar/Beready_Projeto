<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Erro de validação de regra de negócio (fora do validator do ORM).
 * O ResourceErrorTrait converte em 422 com `errors` no mesmo formato do CakePHP:
 * { campo: { regra: mensagem } }.
 */
class ValidationException extends \RuntimeException
{
    /**
     * @param array<string, array<string, string>> $errors
     */
    public function __construct(string $message, private array $errors = [])
    {
        parent::__construct($message, 422);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
