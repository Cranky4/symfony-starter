<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Service\Downloader;

use RuntimeException;

/**
 * Простая реализация загрузчика
 */
class Downloader
{
    /**
     * @throws RuntimeException
     */
    public function getFileContent(string $url): string
    {
        $urlParts = parse_url($url);
        if (empty($urlParts['host']) || empty($urlParts['path'])) {
            throw new RuntimeException('Cannot parse image path');
        }

        $norm = sprintf('%s://%s%s', $urlParts['scheme'] ?? 'http', $urlParts['host'], $urlParts['path']);

        $content = file_get_contents($norm);
        if ($content === false) {
            throw new RuntimeException('Cannot get image content');
        }

        return $content;
    }
}
