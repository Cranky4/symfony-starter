<?php

declare(strict_types=1);

namespace App\Hello\Domain\Entity;

use DateTimeImmutable;

final class HelloWorldEntity
{
    private DateTimeImmutable $createdAt;

    public function __construct(
        private readonly string $id,
        private ?string $message = null,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
