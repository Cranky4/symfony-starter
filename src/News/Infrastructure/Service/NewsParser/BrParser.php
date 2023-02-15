<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Service\NewsParser;

use App\News\Infrastructure\Service\NewsParser\Exception\ParsingErrorException;
use DateTimeImmutable;
use Generator;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

final readonly class BrParser implements NewsParserInterface
{
    public function __construct(
        private string $url,
    ) {
    }

    public static function getSourceName(): string
    {
        return 'br';
    }

    /**
     * @phpstan-return Generator<string>
     * @throws         RuntimeException
     */
    public function parseLinks(): Generator
    {
        $crawler = $this->prepareCrawler($this->url);

        $newsNodes = $this->tryToParse(
            static fn(): Crawler => $crawler->filterXPath(
                'html/body/div[3]/main/div/div[1]/div[2]/div/div/div[1]/div[2]/div[1]/div/div',
            ),
            'Cannot parse news page'
        );

        $url = (array)parse_url($this->url);
        if (!array_key_exists('host', $url)) {
            throw new RuntimeException('Unknown host');
        }
        if (!array_key_exists('scheme', $url)) {
            throw new RuntimeException('Unknown scheme');
        }

        foreach ($newsNodes as $domElement) {
            $crawler = new Crawler($domElement);

            $relativeUrl = $this->tryToParse(
                static fn(): ?string => $crawler->filterXPath('div/div/div[2]/a')->attr('href'),
                'Cannot parse news links',
            );

            yield sprintf('%s://%s%s', $url['scheme'], $url['host'], $relativeUrl);
        }
    }

    public function parseNewsItem(string $link): NewsDto
    {
        $crawler = $this->prepareCrawler($link);

        $news = [
            'title' => '',
            'short' => '',
            'link'  => '',
        ];

        foreach ($crawler->filterXPath('html/head/meta') as $domElement) {
            if ($domElement->getAttribute('property') === 'og:title') {
                $news['title'] = $this->sanitizeContent($domElement->getAttribute('content'));
            }
            if ($domElement->getAttribute('property') === 'og:description') {
                $news['short'] = mb_substr(
                    $this->sanitizeContent(
                        $domElement->getAttribute('content')
                    ),
                    0,
                    200,
                );
            }
            if ($domElement->getAttribute('property') === 'og:url') {
                $news['link'] = $this->sanitizeContent($domElement->getAttribute('content'));
            }
        }

        $news['image']    = $this->parseImage($crawler);
        $news['content']  = $this->parseContent($crawler);
        $news['dateTime'] = $this->parseDateTime($crawler);

        return new NewsDto(
            source: self::getSourceName(),
            id: explode('id=', $news['link'])[1] ?? throw new RuntimeException('Cannot extract id'),
            title: $news['title'],
            short: $news['short'],
            content: $news['content'],
            link: $news['link'],
            dateTime: new DateTimeImmutable($news['dateTime']),
            image: $news['image'],
        );
    }

    /**
     * TODO: добавить обработку ошибок
     *
     * @throws RuntimeException
     */
    private function prepareCrawler(string $link): Crawler
    {
        $content = file_get_contents($link);
        if ($content === false) {
            throw new RuntimeException('Cannot get html');
        }

        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        return $crawler;
    }

    /**
     * @throws ParsingErrorException
     */
    private function tryToParse(callable $fn, string $message): mixed
    {
        try {
            return $fn();
        } catch (InvalidArgumentException $exception) {
            throw new ParsingErrorException($message, 0, $exception);
        }
    }

    private function parseDateTime(Crawler $crawler): string
    {
        $paths = [
            'html/body/div[3]/main/div[1]/div[2]/div[2]/div[2]/div/div[2]/div/div[2]/span[1]',
            'html/body/div[3]/main/div[1]/div[2]/div[3]/div[2]/div/div[2]/div/div[2]/span[1]',
        ];

        foreach ($paths as $path) {
            try {
                return $crawler->filterXPath($path)->text();
            } catch (InvalidArgumentException) {
                continue;
            }
        }

        throw new ParsingErrorException('Cannot parse datetime');
    }

    private function parseImage(Crawler $crawler): ?string
    {
        try {
            return $crawler->filterXPath(
                'html/body/div[3]/main/div[1]/div[2]/div[3]/div[3]/div[1]/div[1]/img',
            )->attr('src');
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    private function parseContent(Crawler $crawler): string
    {
        $paths = [
            'html/body/div[3]/main/div[1]/div[2]/div[3]/div[3]/div[1]/div[2]/div[2]/div[1]',
            'html/body/div[3]/main/div[1]/div[2]/div[3]/div[3]/div[2]/div[1]',
        ];

        foreach ($paths as $path) {
            try {
                return $this->sanitizeContent($crawler->filterXPath($path)->html());
            } catch (InvalidArgumentException) {
                continue;
            }
        }

        throw new ParsingErrorException('Cannot parse content');
    }

    /**
     * TODO: добавить дальнейшую очистку текста
     */
    private function sanitizeContent(string $html): string
    {
        return strip_tags(trim($html), '<p>');
    }
}
