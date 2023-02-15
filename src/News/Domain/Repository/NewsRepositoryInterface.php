<?php

declare(strict_types=1);

namespace App\News\Domain\Repository;

use App\News\Domain\Entity\News;
use App\News\Domain\ValueObject\NewsSource;
use DateTimeImmutable;

interface NewsRepositoryInterface
{
    public function save(News $newsItem): void;

    /**
     * @phpstan-param  array<string, 'ASC'|'DESC'> $order
     * @phpstan-return array<scalar, News>
     */
    public function index(DateTimeImmutable $day, int $page = 1, int $perPage = 10, array $order = []): array;

    public function existBySource(NewsSource $source): bool;

    public function countByDay(DateTimeImmutable $day): int;
}
