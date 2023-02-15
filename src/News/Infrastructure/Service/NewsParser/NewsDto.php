<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Service\NewsParser;

use DateTimeImmutable;

final readonly class NewsDto
{
    public function __construct(
        public string $source,
        public string $id,
        public string $title,
        public string $short,
        public string $content,
        public string $link,
        public DateTimeImmutable $dateTime,
        public ?string $image = null,
    ) {
    }
}