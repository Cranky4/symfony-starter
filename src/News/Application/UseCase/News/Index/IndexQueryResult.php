<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\News\Index;

use App\News\Domain\Entity\News;

final readonly class IndexQueryResult
{
    /**
     * @phpstan-var array<scalar, News>
     */
    private array $items;

    public function __construct(public int $total, News ...$items)
    {
        $this->items = $items;
    }

    /**
     * @return array<scalar, News>
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
