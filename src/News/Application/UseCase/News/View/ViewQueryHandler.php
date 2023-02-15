<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\News\View;

use App\News\Application\Exception\NewsNotFoundException;
use App\News\Domain\Repository\NewsRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ViewQueryHandler
{
    public function __construct(
        private readonly NewsRepositoryInterface $newsRepository,
    ) {
    }

    public function __invoke(ViewQuery $query): ViewQueryResult
    {
        $news = $this->newsRepository->findById($query->id);

        if ($news === null) {
            throw new NewsNotFoundException();
        }

        return new ViewQueryResult(news: $news);
    }
}
