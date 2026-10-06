<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use Cake\ORM\TableRegistry;

class UserRepository implements UserRepositoryInterface
{
    private const PUBLIC_FIELDS = [
        'id',
        'nome',
        'email',
        'role',
        'status',
        'telefone',
        'nivel_ingles',
        'idioma_preferido',
        'objetivos_aprendizado',
        'foto_perfil',
        'uuid',
        'criado_em',
        'atualizado_em',
        'ultimo_login',
    ];

    private $usersTable;

    public function __construct()
    {
        $this->usersTable = TableRegistry::getTableLocator()->get('Users');
    }

    /**
     * Inclui senha_hash (oculto no toArray) para quem precisa conferir a senha.
     */
    public function findById(int $id): ?array
    {
        return $this->findOne(['id' => $id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->findOne(['email' => $email]);
    }

    public function create(array $data, array $protected = []): array
    {
        $user = $this->usersTable->newEntity($data);
        // Campos protegidos não são atribuíveis em massa: guard desligado de propósito
        $user->patch($protected + ['role' => 'user'], ['guard' => false]);
        $this->usersTable->saveOrFail($user);

        return $user->toArray();
    }

    public function update(int $id, array $data, array $protected = []): array
    {
        $user = $this->usersTable->get($id);
        $user = $this->usersTable->patchEntity($user, $data);
        if ($protected) {
            $user->patch($protected, ['guard' => false]);
        }
        $this->usersTable->saveOrFail($user);

        return $user->toArray();
    }

    public function delete(int $id): bool
    {
        $user = $this->usersTable->get($id);
        return $this->usersTable->delete($user);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = $this->usersTable->find()->where(['email' => $email]);
        if ($excludeId) {
            $query->where(['id !=' => $excludeId]);
        }
        return $query->count() > 0;
    }

    public function findByUuid(string $uuid): ?array
    {
        $user = $this->usersTable->find()
            ->select(['id', 'nome', 'email', 'role', 'status', 'foto_perfil', 'uuid'])
            ->where(['uuid' => $uuid])
            ->first();

        return $user ? $user->toArray() : null;
    }

    public function findByResetTokenHash(string $tokenHash): ?array
    {
        $user = $this->usersTable->find()
            ->select(['id', 'nome', 'email', 'role'])
            ->where(['reset_token' => $tokenHash, 'reset_token_expires >' => date('Y-m-d H:i:s')])
            ->first();

        return $user ? $user->toArray() : null;
    }

    public function updateResetToken(int $id, ?string $tokenHash, ?string $expires): bool
    {
        $user = $this->usersTable->get($id);
        $user->patch(['reset_token' => $tokenHash, 'reset_token_expires' => $expires], ['guard' => false]);

        return (bool)$this->usersTable->save($user);
    }

    private function findOne(array $conditions): ?array
    {
        $user = $this->usersTable->find()
            ->select(array_merge(self::PUBLIC_FIELDS, ['senha_hash']))
            ->where($conditions)
            ->first();

        if (!$user) {
            return null;
        }

        $data = $user->toArray();
        $data['senha_hash'] = $user->senha_hash;

        if (empty($data['role'])) {
            $data['role'] = 'user';
        }

        return $data;
    }
}
