<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Http\Resolver;

use App\Common\Infrastructure\Serializer\JsonNormalizer;
use Generator;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class RequestDtoResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    public function supports(ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        if ($className === null || !class_exists($className)) {
            return false;
        }

        $reflection = new ReflectionClass($className);
        if ($reflection->implementsInterface(RequestDTOInterface::class)) {
            return true;
        }

        return false;
    }

    /**
     * @phpstan-return Generator<RequestDTOInterface>
     * @throws         ResolverValidationException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        // creating new instance of custom request DTO
        $class = $argument->getType();
        if (!$class || !class_exists($class)) {
            throw new ResolverValidationException(new ConstraintViolationList(), 'Class not found');
        }

        $format  = JsonNormalizer::FORMAT;
        $context = [];

        if ($request->getMethod() === Request::METHOD_GET) {
            $content = json_encode($request->query->all(), JSON_THROW_ON_ERROR);
        } else {
            $content = $request->getContent();
        }

        if (!$content) {
            throw new ResolverValidationException(new ConstraintViolationList(), 'Content is invalid');
        }

        try {
            $dto = $this->serializer->deserialize($content, $class, $format, $context);
        } catch (MissingConstructorArgumentsException $exception) {
            throw new ResolverValidationException(new ConstraintViolationList(), $exception->getMessage());
        }

        // throw bad request exception in case of invalid request data
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ResolverValidationException($errors, 'Validation error');
        }

        yield $dto;
    }
}
