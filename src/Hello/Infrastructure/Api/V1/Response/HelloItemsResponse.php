<?php

declare(strict_types=1);

namespace App\Hello\Infrastructure\Api\V1\Response;

use OpenApi\Attributes as OA;

/**
 * @phpstan-import-type TResp from HelloResponse as TItem
 *
 * @phpstan-type TResp array{
 *     count: int,
 *     items: array<scalar, TItem>
 * }
 */
#[OA\Schema(
    required: ['createdAt']
)]
readonly class HelloItemsResponse
{
    /**
     * @phpstan-param array<scalar, HelloResponse> $items
     */
    public function __construct(
        #[OA\Property(
            type: 'string',
            format: 'datetime',
            example: '2021-01-03T02:30:00+01:00',
            nullable: false,
        )]
        public int $count,
        #[OA\Property(
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/HelloResponse',
            )
        )]
        public array $items,
    ) {
    }

    /**
     * @phpstan-return TResp
     */
    public function toArray(): array
    {
        return [
            'count' => $this->count,
            'items' => array_map(
                static fn(HelloResponse $i): array => $i->toArray(),
                $this->items,
            ),
        ];
    }
}
