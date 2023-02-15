<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Service\NewsParser;

use Generator;

interface NewsParserInterface
{
    public static function getSourceName(): string;

    public function parseNewsItem(string $link): NewsDto;

    /**
     * @phpstan-return Generator<scalar, string>
     */
    public function parseLinks(): Generator;
}
