<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\News\View;

use App\News\Domain\Entity\News;

readonly final class ViewQueryResult
{
    public function __construct(
        public News $news
    ) {
    }
}
