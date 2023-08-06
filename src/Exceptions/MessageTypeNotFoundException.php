<?php

declare(strict_types=1);

namespace Matmper\Exceptions;

use Exception;
use Throwable;

class MessageTypeNotFoundException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message,
        int $code = 0,
        Throwable $previous = null,
    ) {
        $message = "Webhook message type not found: {$message}";
        parent::__construct($message, ($code ?: 500), $previous);
    }
}
