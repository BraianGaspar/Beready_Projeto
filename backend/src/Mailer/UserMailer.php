<?php
declare(strict_types=1);

namespace App\Mailer;

use Cake\Mailer\Mailer;

class UserMailer extends Mailer
{
    public function resetPassword($user, $token)
    {
        $this->setTransport('default')
            ->setFrom(['noreply@beready.com' => 'BeReady'])
            ->setTo($user->email)
            ->setSubject('Recuperação de Senha - BeReady')
            ->setEmailFormat('html')
            ->setViewVars([
                'nome' => $user->nome,
                'token' => $token,
                'expires' => date('d/m/Y H:i', strtotime('+1 hour'))
            ])
            ->setTemplate('reset_password');
    }
}