<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Http\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsEventListener(
    event: ExceptionEvent::class,
)]
final class HandlerFailedErrorListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof HandlerFailedException) {
            return;
        }

        $responseData = [
            'code'    => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $exception->getPrevious()?->getMessage() ?? $exception->getMessage(),
        ];

        $event->setResponse(new JsonResponse($responseData, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
