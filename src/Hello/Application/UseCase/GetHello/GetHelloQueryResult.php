<?php

declare(strict_types=1);

namespace App\Hello\Application\UseCase\GetHello;

use App\Hello\Domain\Entity\HelloWorldEntity;

final readonly class GetHelloQueryResult
{
    /**
     * @var array<scalar, \App\Hello\Domain\Entity\HelloWorldEntity>
     */
    private array $items;

    public function __construct(
        public int $count,
        HelloWorldEntity ...$items,
    ) {
        $this->items = $items;
    }

    /**
     * @return array<scalar, \App\Hello\Domain\Entity\HelloWorldEntity>
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
