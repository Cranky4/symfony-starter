<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Api\V1\Controller;

use App\News\Application\UseCase\News\Index\IndexQuery;
use App\News\Application\UseCase\News\Index\IndexQueryResult;
use App\News\Domain\Entity\News;
use App\News\Infrastructure\Api\V1\Request\NewsRequest;
use App\News\Infrastructure\Api\V1\Response\NewsItemResponse;
use App\News\Infrastructure\Api\V1\Response\NewsResponse;
use DateTimeImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class NewsController
{
    use HandleTrait;

    private const PATH = '/api/v1/news';

    public function __construct(
        MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    #[
        OA\Post(
            path: self::PATH,
            operationId: __CLASS__,
            summary: 'News',
            tags: ['News']
        ),

        OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/NewsRequest',
            ),
        ),
        OA\Response(
            response: '200',
            description: 'OK',
            content: new OA\JsonContent(
                ref: '#/components/schemas/NewsResponse',
            )
        ),
    ]
    #[Route(
        path: self::PATH,
        name: 'news_index',
        methods: [
            Request::METHOD_GET,
        ],
    )]
    public function __invoke(NewsRequest $request): Response
    {
        $result = $this->handle(
            new IndexQuery(
                day: $request->day ? new DateTimeImmutable($request->day) : new DateTimeImmutable(),
                page: (int)$request->page,
                perPage: (int)$request->perPage,
            )
        );

        if (!$result instanceof IndexQueryResult) {
            throw new \RuntimeException('Unexpected result');
        }

        return new JsonResponse(
            new NewsResponse(
                total: $result->total,
                items: array_map(
                    static fn(News $n): NewsItemResponse => new NewsItemResponse(
                        id: $n->getId(),
                        title: $n->getTitle(),
                        short: $n->getShort(),
                        content: $n->getContent(),
                        day: $n->getDay(),
                        time: $n->getTime(),
                        image: $n->getImage()
                    ),
                    $result->getItems(),
                ),
            )
        );
    }
}
