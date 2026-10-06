<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\TagRepositoryInterface;

class TagService
{
    private TagRepositoryInterface $repository;

    public function __construct(TagRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getTagsVisibleToUsuario(int $usuarioId): array
    {
        return $this->repository->findVisibleToUsuario($usuarioId);
    }

    public function getTagById(int $id): array
    {
        $tag = $this->repository->findById($id);
        if (!$tag) {
            throw new \RuntimeException('Tag não encontrada', 404);
        }
        return $tag;
    }

    public function getTagsByUsuario(int $usuarioId): array
    {
        return $this->repository->findByUsuarioId($usuarioId);
    }

    /**
     * Só admin pode marcar tag_sistema (tag visível a todos).
     */
    public function createTag(int $usuarioId, array $data, bool $podeTagSistema = false): array
    {
        if (empty($data['nome'])) {
            throw new \InvalidArgumentException('Nome da tag é obrigatório');
        }

        $data['criado_por'] = $usuarioId;
        if (!$podeTagSistema) {
            unset($data['tag_sistema']);
        }

        $existing = $this->repository->findByName($data['nome'], $usuarioId);
        if ($existing) {
            throw new \RuntimeException('Tag já existe', 409);
        }

        return $this->repository->create($data);
    }

    /**
     * O chamador deve validar o dono antes (getTagById).
     */
    public function updateTag(int $id, array $data, bool $podeTagSistema = false): array
    {
        $tag = $this->getTagById($id);

        unset($data['criado_por']);
        if (!$podeTagSistema) {
            unset($data['tag_sistema']);
        }

        // Verifica se novo nome já existe (se for diferente)
        if (isset($data['nome']) && $data['nome'] !== $tag['nome']) {
            $existing = $this->repository->findByName($data['nome'], $tag['criado_por']);
            if ($existing) {
                throw new \RuntimeException('Tag já existe', 409);
            }
        }

        return $this->repository->update($id, $data);
    }

    public function deleteTag(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
