<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Http\Resolver;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ResolverValidationException extends Exception
{
    public function __construct(
        private readonly ConstraintViolationListInterface $errors,
        string $message,
    ) {
        parent::__construct($message);
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
