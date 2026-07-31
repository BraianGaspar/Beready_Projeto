<?php

declare(strict_types=1);

namespace App\Mailer;

use Cake\Mailer\Mailer;
use function Cake\Core\env;

class UserMailer extends Mailer
{

    /**
     * E-mail de boas-vindas após registro
     */
    public function welcome($user): void
    {

        $frontendUrl = env('APP_BASE_URL');
        $loginLink = $frontendUrl . 'login';

        try {
            $this->setTransport('default')
                ->setFrom([env('EMAIL_FROM') => env('EMAIL_FROM_NAME')])
                ->setTo($user->email)
                ->setSubject('Bem-vindo ao BeReady!')
                ->setEmailFormat('html')
                ->viewBuilder()
                    ->setTemplate('welcome')
                    ->setVars([
                        'nome' => $user->nome ?? 'Usuário',
                        'loginLink' => $loginLink,
                        'ano' => date('Y')
                    ]);

            error_log("Mailer configurado, tentando deliver...");

            $this->deliver();

            error_log("E-mail de boas-vindas enviado para: " . $user->email);

        } catch (\Exception $e) {
            error_log("ERRO no Mailer (welcome): " . $e->getMessage());
            error_log("Arquivo: " . $e->getFile() . " linha: " . $e->getLine());
            // Não lança exceção para não quebrar o registro
        }
    }

    public function resetPassword($user, string $token): void
    {
        $frontendUrl = env('APP_BASE_URL');
        $resetLink = $frontendUrl . 'reset-password/' . $token;

        try {
            $this->setTransport('default')
                ->setFrom([env('EMAIL_FROM') => env('EMAIL_FROM_NAME')])
                ->setTo($user->email)
                ->setSubject('Recuperação de Senha - BeReady')
                ->setEmailFormat('html')
                ->viewBuilder()
                    ->setTemplate('reset_password')
                    ->setVars([
                        'nome' => $user->nome,
                        'resetLink' => $resetLink,
                        'expires' => date('d/m/Y H:i', strtotime('+1 hour'))
                    ]);

            $this->deliver();

        } catch (\Exception $e) {
            throw $e;
        }
    }
}