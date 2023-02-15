<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Service\NewsParser;

use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class NewsParser
{
    public const NEWS_PARSER_TAG = 'news.parsers.parser';

    private ServiceLocator $parsersLocator;

    public function __construct(
        #[TaggedLocator(
            tag: self::NEWS_PARSER_TAG,
            defaultIndexMethod: 'getSourceName',
        )]
        ServiceLocator $parsersLocator,
    ) {
        $this->parsersLocator = $parsersLocator;
    }

    public function parse(string $sourceName)
    {
        $parser = $this->parsersLocator->get($sourceName);

        dd($parser);

        //TODO:
    }
}
