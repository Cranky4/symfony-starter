<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\News\Index;

use DateTimeImmutable;

final readonly class IndexQuery
{
    public function __construct(
        public DateTimeImmutable $day,
        public int $page = 1,
        public int $perPage = 5,
    ) {
    }
}
