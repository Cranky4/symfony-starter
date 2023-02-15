<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Api\V1\Controller;

use App\News\Infrastructure\Api\V1\Request\NewsRequest;
use App\News\Infrastructure\Service\NewsParser\BrParser;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class NewsController
{
    private const PATH = '/api/v1/news';

    public function __construct(
        private readonly BrParser $parser,
    ) {
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
// OA\Response(
// response: '200',
// description: 'OK',
// content: new OA\JsonContent(
// ref: '#/components/schemas/HelloItemsResponse',
// )
// ),
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
        dd($this->parser->parse());
    }
}
