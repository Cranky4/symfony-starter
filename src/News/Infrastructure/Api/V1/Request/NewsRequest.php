<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Api\V1\Request;

use App\Common\Infrastructure\Http\Resolver\RequestDTOInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema]
readonly class NewsRequest implements RequestDTOInterface
{
    public function __construct(
        #[
            Assert\NotBlank(allowNull: true),
            Assert\Type(type: 'numeric'),
            Assert\Length(min: 1),
        ]
        #[OA\Property(
            type: 'int',
            default: '1',
            example: '1',
            nullable: false,
        )]
        public string $page = '1',
        #[
            Assert\NotBlank(allowNull: true),
            Assert\Type(type: 'numeric'),
            Assert\Length(min: 1, max: 20),
        ]
        #[OA\Property(
            type: 'int',
            default: '10',
            example: '10',
            nullable: false,
        )]
        public string $perPage = '10',
    ) {
    }
}
