<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Service\NewsParser;

use App\News\Application\UseCase\News\Create\CreateCommand;
use Generator;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class NewsParser
{
    use HandleTrait;

    public const NEWS_PARSER_TAG = 'news.parsers.parser';

    private ServiceLocator $parsersLocator;

    public function __construct(
        #[TaggedLocator(
            tag: self::NEWS_PARSER_TAG,
            defaultIndexMethod: 'getSourceName',
        )]
        ServiceLocator $parsersLocator,
        MessageBusInterface $commandBus,
    ) {
        $this->parsersLocator = $parsersLocator;
        $this->messageBus     = $commandBus;
    }

    /**
     * @phpstan-return Generator<string>
     */
    public function getLinks(string $sourceName): Generator
    {
        /** @var NewsParserInterface $parser */
        $parser = $this->parsersLocator->get($sourceName);

        return $parser->parseLinks();
    }

    public function parseAndSave(string $sourceName, string $link): void
    {
        /** @var NewsParserInterface $parser */
        $parser = $this->parsersLocator->get($sourceName);

        $item = $parser->parseNewsItem($link);

        $this->handle(
            new CreateCommand(
                id: Uuid::uuid7()->toString(),
                source: $item->source,
                sourceId: $item->id,
                title: $item->title,
                short: $item->short,
                content: $item->content,
                dateTime: $item->dateTime,
                image: $item->image,
            )
        );
    }
}
