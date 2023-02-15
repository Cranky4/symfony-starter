<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Api\V1\Response;

use DateTimeImmutable;
use OpenApi\Attributes as OA;

/**
 * @phpstan-type TResp array{
 *      id: string,
 *      title: string,
 *      short:string,
 *      day: string,
 *      time: string,
 *      content: string,
 *      image:?string
 * }
 */
#[OA\Schema(
    required: [
        'id',
        'createdAt',
    ]
)]
readonly class NewsSingleResponse
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
            nullable: false,
        )]
        public string $title,
        #[OA\Property(
            type: 'string',
            nullable: false,
        )]
        public string $short,
        #[OA\Property(
            type: 'string',
            nullable: false,
        )]
        public string $content,
        #[OA\Property(
            type: 'string',
            format: 'date',
            nullable: false,
        )]
        public DateTimeImmutable $day,
        #[OA\Property(
            type: 'string',
            format: 'time',
            nullable: false,
        )]
        public string $time,
        #[OA\Property(
            type: 'string',
            format: 'url',
            nullable: true,
        )]
        public ?string $image = null,
    ) {
    }

    /**
     * @phpstan-return TResp
     */
    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'title'   => $this->title,
            'short'   => $this->short,
            'day'     => $this->day->format('Y.m.d'),
            'time'    => $this->time,
            'content' => htmlentities($this->content),
            'image'   => $this->image,
        ];
    }
}
