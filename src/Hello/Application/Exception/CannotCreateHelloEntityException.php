<?php

declare(strict_types=1);

namespace App\Hello\Application\Exception;

use Exception;
use Throwable;

class CannotCreateHelloEntityException extends Exception
{
    private const MESSAGE = 'Cannot create hello entity';

    public function __construct(?string $reason = null, ?Throwable $previous = null)
    {
        $message = self::MESSAGE;

        if ($reason !== null) {
            $message .= sprintf(': %s', $reason);
        }

        parent::__construct($message, 0, $previous);
    }
}
