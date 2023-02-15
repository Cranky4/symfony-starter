<?php

declare(strict_types=1);

namespace App\News\Domain\Entity;

use App\News\Domain\ValueObject\NewsSource;
use DateTimeImmutable;

final class News
{
    private DateTimeImmutable $createdAt;

    public function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly string $content,
        private readonly string $short,
        private readonly DateTimeImmutable $day,
        private readonly string $time,
        private readonly ?string $image = null,
        private readonly ?NewsSource $source = null,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getShort(): string
    {
        return $this->short;
    }

    public function getDay(): DateTimeImmutable
    {
        return $this->day;
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getSource(): ?NewsSource
    {
        return $this->source;
    }
}
