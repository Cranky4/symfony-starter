<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Service\NewsParser;

use Generator;

interface NewsParserInterface
{
    public static function getSourceName(): string;

    public function parseNewsItem(string $link): NewsDto;

    /**
     * @phpstan-return Generator<string>
     *
     * TODO: можно добавить сюда доп параметры (например: количество или доп GET-параметры для запроса ссылок)
     */
    public function parseLinks(): Generator;
}
