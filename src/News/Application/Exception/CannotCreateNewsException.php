<?php

declare(strict_types=1);

namespace App\News\Application\Exception;

use Throwable;

class CannotCreateNewsException extends \Exception
{
    private const MESSAGE = 'Cannot create news entity';

    public function __construct(?string $reason = null, ?Throwable $previous = null)
    {
        $message = self::MESSAGE;

        if ($reason !== null) {
            $message .= sprintf(': %s', $reason);
        }

        parent::__construct($message, 0, $previous);
    }
}
