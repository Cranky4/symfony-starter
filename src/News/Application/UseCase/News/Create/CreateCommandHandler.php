<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\News\Create;

use App\News\Application\Exception\CannotCreateNewsException;
use App\News\Domain\Entity\News;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\News\Domain\ValueObject\NewsSource;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
readonly class CreateCommandHandler
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository,
    ) {
    }

    public function __invoke(CreateCommand $command): void
    {
        $source = new NewsSource(
            name: $command->source,
            id: $command->sourceId,
        );

        if ($this->newsRepository->existBySource($source)) {
            throw new CannotCreateNewsException('News item with this source already exists');
        }

        $news = new News(
            id: $command->id,
            title: $command->title,
            content: $command->content,
            short: $command->short,
            day: $command->dateTime,
            time: $command->dateTime->format('H:i:s'),
            image: $command->image,
            source: $source,
        );

        $this->newsRepository->save($news);
    }
}
