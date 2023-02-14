<?php

declare(strict_types=1);

namespace App\Hello\Infrastructure\Api\V1\Request;

use App\Common\Infrastructure\Http\Resolver\RequestDTOInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema]
readonly class HelloRequest implements RequestDTOInterface
{
    public function __construct(
        #[
            Assert\NotBlank(allowNull: false),
            Assert\Uuid,
        ]
        #[OA\Property(
            type: 'string',
            format: 'uuid',
            example: 'My name is John!',
            nullable: false,
        )]
        public string $id,
        #[Assert\NotBlank(allowNull: true)]
        #[OA\Property(
            type: 'string',
            example: 'My name is John!',
            nullable: true,
        )]
        public ?string $message = null,
    ) {
    }
}
