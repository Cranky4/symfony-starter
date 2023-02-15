<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\News\Index;

use App\News\Domain\Repository\NewsRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class IndexQueryHandler
{
    public function __construct(
        private readonly NewsRepositoryInterface $newsRepository,
    ) {
    }

    public function __invoke(IndexQuery $query): IndexQueryResult
    {
        $total = $this->newsRepository->countByDay($query->day);

        if ($total === 0) {
            return new IndexQueryResult(0);
        }

        $news = $this->newsRepository->index(
            $query->day,
            $query->page,
            $query->perPage,
            ['time' => 'DESC']
        );

        return new IndexQueryResult($total, ...$news);
    }
}
