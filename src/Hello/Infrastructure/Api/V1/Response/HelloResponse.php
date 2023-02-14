<?php

declare(strict_types=1);

namespace App\Hello\Infrastructure\Api\V1\Response;

use DateTimeImmutable;
use OpenApi\Attributes as OA;

/**
 * @phpstan-type TResp array{
 *     id: string,
 *     createdAt: string,
 * }
 */
#[OA\Schema(
    required: [
        'id',
        'createdAt',
    ]
)]
readonly class HelloResponse
{
    public function __construct(
        #[OA\Property(
            type: 'string',
            format: 'uuid',
            nullable: false,
        )]
        public string $id,
        #[OA\Property(
            type: 'string',
            format: 'datetime',
            example: '2021-01-03T02:30:00+01:00',
            nullable: false,
        )]
        public DateTimeImmutable $createdAt,
        #[OA\Property(
            type: 'string',
            nullable: true,
        )]
        public ?string $message = null,
    ) {
    }

    /**
     * @phpstan-return TResp
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'message'   => $this->message,
            'createdAt' => $this->createdAt->format(DATE_ATOM),
        ];
    }
}
