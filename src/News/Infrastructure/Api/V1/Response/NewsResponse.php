<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Api\V1\Response;

use OpenApi\Attributes as OA;

/**
 * @phpstan-import-type TResp from NewsItemResponse as TItem
 *
 * @phpstan-type TResp array{
 *     count: int,
 *     items: array<scalar, TItem>
 * }
 */
#[OA\Schema(
    required: ['createdAt']
)]
readonly class NewsResponse
{
    /**
     * @phpstan-param array<scalar, NewsItemResponse> $items
     */
    public function __construct(
        #[OA\Property(
            type: 'string',
            format: 'datetime',
            example: '2021-01-03T02:30:00+01:00',
            nullable: false,
        )]
        public int $total,
        #[OA\Property(
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/NewsItemResponse',
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
            'count' => $this->total,
            'items' => array_map(
                static fn(NewsItemResponse $i): array => $i->toArray(),
                $this->items,
            ),
        ];
    }
}
