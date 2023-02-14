<?php

declare(strict_types=1);

namespace App\Hello\Application\UseCase\CreateHello;

use App\Hello\Application\Exception\CannotCreateHelloEntityException;
use App\Hello\Domain\Entity\HelloWorldEntity;
use App\Hello\Domain\Repository\HelloRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateHelloCommandHandler
{
    public function __construct(
        private HelloRepositoryInterface $helloRepository,
    ) {
    }

    /**
     * @throws CannotCreateHelloEntityException
     */
    public function __invoke(CreateHelloCommand $command): void
    {
        $exists = $this->helloRepository->existsById($command->id);
        if ($exists) {
            throw new CannotCreateHelloEntityException(
                sprintf('Hello with id %s already exists', $command->id)
            );
        }

        $entity = new HelloWorldEntity(
            id: $command->id,
            message: $command->message,
        );

        $this->helloRepository->save($entity);
    }
}
