<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Http\Listener;

use App\Common\Infrastructure\Http\Resolver\ResolverValidationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(
    event:ExceptionEvent::class,
)]
final class ValidationErrorListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ResolverValidationException) {
            return;
        }

        $errors = [];
        foreach ($exception->getErrors() as $error) {
            $errors[] = [
                'message' => $error->getMessage(),
                'path'    => $error->getPropertyPath(),
                'code'    => $error->getCode(),
            ];
        }

        $responseData = [
            'code'    => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $exception->getMessage(),
            'errors'  => $errors,
        ];

        $event->setResponse(new JsonResponse($responseData, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
