<?php

declare(strict_types=1);

namespace App\Hello\Infrastructure\Persistence\InMemory;

use App\Hello\Domain\Entity\HelloWorldEntity;
use App\Hello\Domain\Repository\HelloRepositoryInterface;

class HelloRepository implements HelloRepositoryInterface
{
    /**
     * @var array<string, HelloWorldEntity>
     */
    private array $entities = [];

    public function __construct()
    {
        $this->entities['acd5e48a-3a6d-4711-8be6-61f8d3ba3380'] = new HelloWorldEntity(
            id: 'acd5e48a-3a6d-4711-8be6-61f8d3ba3380',
            message: 'First'
        );

        $this->entities['acd5e48a-3a6d-4711-8be6-61f8d3ba3381'] = new HelloWorldEntity(
            id: 'acd5e48a-3a6d-4711-8be6-61f8d3ba3381',
            message: 'Second'
        );
    }

    /**
     * @return array<scalar, \App\Hello\Domain\Entity\HelloWorldEntity>
     */
    public function getAll(): array
    {
        return $this->entities;
    }

    public function save(HelloWorldEntity $entity): void
    {
        $this->entities[$entity->getId()] = $entity;
    }

    public function total(): int
    {
        return count($this->entities);
    }

    public function existsById(string $id): bool
    {
        return array_key_exists($id, $this->entities);
    }
}
