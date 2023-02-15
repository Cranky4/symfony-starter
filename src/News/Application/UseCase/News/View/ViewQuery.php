<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\News\View;

final readonly class ViewQuery
{
    public function __construct(
        public string $id
    ) {
    }
}
