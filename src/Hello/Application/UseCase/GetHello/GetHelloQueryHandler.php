<?php

declare(strict_types=1);

namespace App\Hello\Application\UseCase\GetHello;

use App\Hello\Domain\Repository\HelloRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetHelloQueryHandler
{
    public function __construct(
        private readonly HelloRepositoryInterface $helloRepository,
    ) {
    }

    public function __invoke(GetHelloQuery $query): GetHelloQueryResult
    {
        $count = $this->helloRepository->total();
        $items = $this->helloRepository->getAll();

        return new GetHelloQueryResult(
            $count,
            ...$items
        );
    }
}
