<?php

declare(strict_types=1);

namespace App\News\Domain\ValueObject;

readonly final class NewsSource
{
    public function __construct(
        public string $name,
        public string $id,
    ) {
    }
}
