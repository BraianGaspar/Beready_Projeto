<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\TagRepositoryInterface;
use Cake\ORM\TableRegistry;

class TagRepository implements TagRepositoryInterface
{
    private $table;

    public function __construct()
    {
        $this->table = TableRegistry::getTableLocator()->get('Tags');
    }

    /**
     * Tags do usuário + tags de sistema (compartilhadas).
     */
    public function findVisibleToUsuario(int $usuarioId): array
    {
        $tags = $this->table->find()
            ->where(['OR' => ['criado_por' => $usuarioId, 'tag_sistema' => true]])
            ->orderBy(['nome' => 'ASC'])
            ->all();
        return array_map(fn($t) => $t->toArray(), $tags->toArray());
    }

    public function findById(int $id): ?array
    {
        $tag = $this->table->find()->where(['id' => $id])->first();
        return $tag ? $tag->toArray() : null;
    }

    public function findByUsuarioId(int $usuarioId): array
    {
        $tags = $this->table->find()
            ->where(['criado_por' => $usuarioId])
            ->orderBy(['nome' => 'ASC'])
            ->all();
        return array_map(fn($t) => $t->toArray(), $tags->toArray());
    }

    /**
     * O nome é único por dono (índice tags_criado_por_nome_key).
     */
    public function findByName(string $name, ?int $criadoPor): ?array
    {
        $tag = $this->table->find()->where(['nome' => $name, 'criado_por IS' => $criadoPor])->first();
        return $tag ? $tag->toArray() : null;
    }

    public function create(array $data): array
    {
        $tag = $this->table->newEntity($data);
        $this->table->saveOrFail($tag);
        return $tag->toArray();
    }

    public function update(int $id, array $data): array
    {
        $tag = $this->table->get($id);
        $tag = $this->table->patchEntity($tag, $data);
        $this->table->saveOrFail($tag);
        return $tag->toArray();
    }

    public function delete(int $id): bool
    {
        $tag = $this->table->get($id);
        return $this->table->delete($tag);
    }
}
