<?php

declare(strict_types=1);

namespace App\Hello\Domain\Repository;

use App\Hello\Domain\Entity\HelloWorldEntity;

interface HelloRepositoryInterface
{
    public function total(): int;

    /**
     * @return array<scalar, HelloWorldEntity>
     */
    public function getAll(): array;

    public function save(HelloWorldEntity $entity): void;

    public function existsById(string $id): bool;
}
