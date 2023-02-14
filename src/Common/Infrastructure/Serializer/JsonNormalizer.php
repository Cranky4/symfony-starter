<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Serializer;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Throwable;

class JsonNormalizer implements DenormalizerInterface
{
    public const FORMAT = 'json';

    /**
     * @phpstan-param class-string $type
     * @phpstan-param array<string, mixed> $context
     *
     * @throws ReflectionException
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $reflection = new ReflectionClass($type);

        $constructor = $reflection->getConstructor();


        if ($constructor === null || true !== $constructor->isPublic()) {
            return $reflection->newInstanceWithoutConstructor();
        }

        $constructorParameters = [];
        foreach ($constructor->getParameters() as $parameter) {
            if ($value = $data[$parameter->getName()] ?? null) {
                $constructorParameters[$parameter->getName()] = $value;
            } elseif ($parameter->isDefaultValueAvailable()) {
                $constructorParameters[$parameter->getName()] = $parameter->getDefaultValue();
            } elseif ($parameter->allowsNull()) {
                $constructorParameters[$parameter->getName()] = null;
            }
        }

        try {
            return $reflection->newInstanceArgs($constructorParameters);
        } catch (Throwable) {
            return $reflection->newInstanceWithoutConstructor();
        }
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $format === self::FORMAT;
    }
}
