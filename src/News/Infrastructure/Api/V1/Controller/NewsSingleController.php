<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Api\V1\Controller;

use App\News\Application\UseCase\News\View\ViewQuery;
use App\News\Application\UseCase\News\View\ViewQueryResult;
use App\News\Infrastructure\Api\V1\Request\NewsRequest;
use App\News\Infrastructure\Api\V1\Response\NewsSingleResponse;
use OpenApi\Attributes as OA;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class NewsSingleController
{
    use HandleTrait;

    private const PATH = '/api/v1/news/{id}';

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
                ref: '#/components/schemas/NewsSingleResponse',
            )
        ),
    ]
    #[Route(
        path: self::PATH,
        name: 'news_view',
        methods: [
            Request::METHOD_GET,
        ],
    )]
    public function __invoke(string $id, NewsRequest $request): Response
    {
        $result = $this->handle(
            new ViewQuery($id)
        );

        if (!$result instanceof ViewQueryResult) {
            throw new RuntimeException('Unexpected result');
        }

        return new JsonResponse(
            (new NewsSingleResponse(
                id: $result->news->getId(),
                title: $result->news->getTitle(),
                short: $result->news->getShort(),
                content: $result->news->getContent(),
                day: $result->news->getDay(),
                time: $result->news->getTime(),
                image: $result->news->getImage(),
            ))->toArray(),
        );
    }
}
