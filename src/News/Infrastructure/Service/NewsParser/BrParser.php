<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Service\NewsParser;

use DateTimeImmutable;
use Generator;
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
     * @phpstan-return Generator<scalar, string>
     */
    public function parseLinks(): Generator
    {
        $crawler = $this->prepareCrawler($this->url);

        $newsNodes = $crawler->filterXPath(
            'html/body/div[3]/main/div/div[1]/div[2]/div/div/div[1]/div[2]/div[1]/div/div',
        );

// TODO: check error
        $url = parse_url($this->url);

        foreach ($newsNodes as $domElement) {
            $crawler     = new Crawler($domElement);
            $relativeUrl = $crawler->filterXPath('div/div/div[2]/a')->attr('href');

            yield sprintf('%s://%s%s', $url['scheme'], $url['host'], $relativeUrl);
        }
    }

    public function parseNewsItem(string $link): NewsDto
    {
        $crawler = $this->prepareCrawler($link);

        $news = [];
        foreach ($crawler->filterXPath('html/head/meta') as $domElement) {
            if ($domElement->getAttribute('property') === 'og:title') {
                $news['title'] = $domElement->getAttribute('content');
            }
            if ($domElement->getAttribute('property') === 'og:image') {
                $news['preview_image'] = $domElement->getAttribute('content');
            }
            if ($domElement->getAttribute('property') === 'og:description') {
                $news['description'] = $domElement->getAttribute('content');
            }
            if ($domElement->getAttribute('property') === 'og:url') {
                $news['link'] = $domElement->getAttribute('content');
            }
        }

        $news['image'] = $crawler->filterXPath(
            'html/body/div[3]/main/div[1]/div[2]/div[3]/div[3]/div[1]/div[1]/img',
        )->attr('src');

        $paragraphs = $crawler
            ->filterXPath('html/body/div[3]/main/div[1]/div[2]/div[3]/div[3]/div[1]/div[2]/div[2]/div[1]/p')
            ->each(
                static fn(Crawler $node): string => sprintf('<p>%s</p>', strip_tags($node->text(), '<p>')),
            );

        $news['content']  = trim(implode($paragraphs));
        $news['dateTime'] = $crawler->filterXPath(
            'html/body/div[3]/main/div[1]/div[2]/div[3]/div[2]/div/div[2]/div/div[2]/span[1]',
        )->text();

        return new NewsDto(
            source: self::getSourceName(),
            id: explode('id=', $news['link'])[1] ?? throw new RuntimeException('Cannot extract id'),
            title: $news['title'],
            image: $news['image'],
            description: $news['description'],
            content: $news['content'],
            link: $news['link'],
            dateTime: new DateTimeImmutable($news['dateTime']),
        );
    }

    private function prepareCrawler(string $link): Crawler
    {
        $content = file_get_contents($link); // optimistic
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        return $crawler;
    }
}
