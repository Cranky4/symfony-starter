<?php

declare(strict_types=1);

namespace App\Hello\Application\UseCase\CreateHello;

readonly class CreateHelloCommand
{
    public function __construct(
        public string $id,
        public ?string $message = null,
    ) {
    }
}
