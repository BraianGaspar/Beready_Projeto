<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserUseCaseInterface;
use App\Contracts\UserRepositoryInterface;
use App\Exceptions\EmailAlreadyExistsException;
use App\Exceptions\WeakPasswordException;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\UserNotFoundException;
use App\Mailer\UserMailer;
use Cake\Log\Log;
use Ramsey\Uuid\Uuid;

class UserService implements UserUseCaseInterface
{
    public const STATUS_ATIVO = 'ativo';

    /**
     * Campos que o próprio usuário pode informar no registro/edição de perfil (além de "senha").
     */
    public const EDITABLE_FIELDS = [
        'nome',
        'email',
        'telefone',
        'nivel_ingles',
        'idioma_preferido',
        'objetivos_aprendizado',
        'foto_perfil',
    ];

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        if (empty($data['nome']) || empty($data['email']) || empty($data['senha'])) {
            throw new \InvalidArgumentException('Nome, e-mail e senha são obrigatórios');
        }
        if (strlen($data['senha']) < 6) {
            throw new WeakPasswordException('A senha deve ter pelo menos 6 caracteres');
        }
        if ($this->userRepository->emailExists($data['email'])) {
            throw new EmailAlreadyExistsException();
        }

        $user = $this->userRepository->create(
            array_intersect_key($data, array_flip(self::EDITABLE_FIELDS)),
            [
                'uuid' => Uuid::uuid4()->toString(),
                'senha_hash' => password_hash($data['senha'], PASSWORD_DEFAULT),
                'role' => 'user',
                'status' => self::STATUS_ATIVO,
            ]
        );

        try {
            (new UserMailer())->welcome((object)$user);
        } catch (\Exception $e) {
            // Não interrompe o registro se o e-mail falhar
            Log::error('Erro ao enviar e-mail de boas-vindas: ' . $e->getMessage());
        }

        return $user;
    }

    public function login(string $email, string $password): array
    {
        if (empty($email) || empty($password)) {
            throw new \InvalidArgumentException('E-mail e senha são obrigatórios');
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user || !password_verify($password, $user['senha_hash'] ?? '')) {
            throw new \RuntimeException('E-mail ou senha inválidos', 401);
        }

        if (!$this->isActive($user)) {
            throw new \RuntimeException('Conta inativa ou bloqueada', 403);
        }

        $this->registerLogin((int)$user['id']);
        unset($user['senha_hash']);

        return $user;
    }

    /**
     * Status nulo (legado) conta como ativo; qualquer outro valor (inativo, bloqueado...) bloqueia o acesso.
     */
    public function isActive(array $user): bool
    {
        return empty($user['status']) || $user['status'] === self::STATUS_ATIVO;
    }

    public function registerLogin(int $id): void
    {
        $this->userRepository->update($id, ['ultimo_login' => date('Y-m-d H:i:s')]);
    }

    public function getUserById(int $id): array
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new UserNotFoundException();
        }
        unset($user['senha_hash']);
        return $user;
    }

    public function getUserByUuid(string $uuid): array
    {
        $user = $this->userRepository->findByUuid($uuid);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    /**
     * @param array $data Campos de EDITABLE_FIELDS e, opcionalmente, "senha" (vira senha_hash)
     * @param array $protected Campos restritos (role/status), já autorizados pelo chamador
     */
    public function updateUser(int $id, array $data, array $protected = []): array
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        if (isset($data['email']) && $this->userRepository->emailExists($data['email'], $id)) {
            throw new EmailAlreadyExistsException('Este e-mail já está em uso');
        }

        if (isset($data['senha']) && $data['senha'] !== '') {
            if (strlen($data['senha']) < 6) {
                throw new WeakPasswordException('A senha deve ter pelo menos 6 caracteres');
            }
            $protected['senha_hash'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }

        return $this->userRepository->update(
            $id,
            array_intersect_key($data, array_flip(self::EDITABLE_FIELDS)),
            $protected
        );
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $this->userRepository->delete($id);
    }

    /**
     * Nunca revela se o e-mail existe: falhas de envio são apenas registradas no log.
     * O banco guarda só o hash SHA-256 do token; o token puro vai apenas no e-mail.
     */
    public function forgotPassword(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->userRepository->updateResetToken((int)$user['id'], hash('sha256', $token), $expires);

        try {
            (new UserMailer())->resetPassword((object)$user, $token);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de recuperação de senha: ' . $e->getMessage());
        }
    }

    public function resetPassword(string $token, string $newPassword): void
    {
        $user = $this->userRepository->findByResetTokenHash(hash('sha256', $token));
        if (!$user) {
            throw new InvalidTokenException('Token inválido ou expirado');
        }
        if (strlen($newPassword) < 6) {
            throw new WeakPasswordException('A senha deve ter pelo menos 6 caracteres');
        }

        $this->userRepository->update((int)$user['id'], [], [
            'senha_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expires' => null,
        ]);
    }
}
