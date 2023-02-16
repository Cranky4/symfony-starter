<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\News\Create;

use App\News\Application\Exception\CannotCreateNewsException;
use App\News\Domain\Entity\News;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\News\Domain\ValueObject\NewsSource;
use League\Flysystem\FilesystemOperator;
use RuntimeException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateCommandHandler
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository,
        private FilesystemOperator $defaultStorage
    ) {
    }

    /**
     * @throws CannotCreateNewsException
     */
    public function __invoke(CreateCommand $command): void
    {
        $source = new NewsSource(
            name: $command->source,
            id: $command->sourceId,
        );

        if ($this->newsRepository->existBySource($source)) {
            throw new CannotCreateNewsException('News item with this source already exists');
        }

        $path = null;
        if ($command->image !== null) {
            $path = $this->saveImageAndGetPath($command->image, $command->id);
        }

        $news = new News(
            id: $command->id,
            title: $command->title,
            content: $command->content,
            short: $command->short,
            day: $command->dateTime,
            time: $command->dateTime->format('H:i:s'),
            image: $path,
            source: $source,
        );

        $this->newsRepository->save($news);
    }

    /**
     * @throws RuntimeException
     */
    private function saveImageAndGetPath(string $image, string $id): string
    {
        $name = md5($image . time());
        $ext  = pathinfo($image, PATHINFO_EXTENSION);
        $path = sprintf('/news/%s/%s.%s', $id, $name, $ext);

        $url = parse_url($image);
        if (empty($url['host']) || empty($url['path'])) {
            throw new RuntimeException('Cannot parse image path');
        }

        $norm = sprintf('%s://%s%s', $url['scheme'] ?? 'http', $url['host'], $url['path']);

        $content = file_get_contents($norm);
        if ($content === false) {
            throw new RuntimeException('Cannot get image content');
        }

        $this->defaultStorage->write($path, $content);

        return $path;
    }
}
