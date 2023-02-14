<?php

declare(strict_types=1);

namespace App\Hello\Infrastructure\Api\V1\Controller;

use App\Hello\Application\UseCase\CreateHello\CreateHelloCommand;
use App\Hello\Application\UseCase\GetHello\GetHelloQuery;
use App\Hello\Application\UseCase\GetHello\GetHelloQueryResult;
use App\Hello\Domain\Entity\HelloWorldEntity;
use App\Hello\Infrastructure\Api\V1\Request\HelloRequest;
use App\Hello\Infrastructure\Api\V1\Response\HelloItemsResponse;
use App\Hello\Infrastructure\Api\V1\Response\HelloResponse;
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
final class HelloController
{
    use HandleTrait;

    private const PATH = '/api/v1/hellos';

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    #[
        OA\Post(
            path: self::PATH,
            operationId: __CLASS__,
            summary: 'Hello world',
            tags: ['Hello']
        ),

        OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/HelloRequest',
            ),
        ),
        OA\Response(
            response: '200',
            description: 'OK',
            content: new OA\JsonContent(
                ref: '#/components/schemas/HelloItemsResponse',
            )
        ),
    ]
    #[Route(
        path: self::PATH,
        name: 'hello_world_list',
        methods: [
            Request::METHOD_POST,
            Request::METHOD_GET,
        ],
    )]
    public function __invoke(HelloRequest $request): Response
    {
        $this->handle(
            new CreateHelloCommand(
                id: $request->id,
                message: $request->message,
            )
        );

        $result = $this->handle(new GetHelloQuery());

        if (!$result instanceof GetHelloQueryResult) {
            throw new RuntimeException('Unexpected result');
        }

        $response = new HelloItemsResponse(
            count: $result->count,
            items: array_map(
                static fn(HelloWorldEntity $e): HelloResponse => new HelloResponse(
                    id: $e->getId(),
                    createdAt: $e->getCreatedAt(),
                    message: $e->getMessage(),
                ),
                $result->getItems(),
            ),
        );

        return new JsonResponse(
            $response->toArray(),
        );
    }
}
