<?php

declare(strict_types=1);

namespace Matmper\Exceptions;

use Exception;
use Throwable;

class EnvironmentNotFoundException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message,
        int $code = 404,
        Throwable $previous = null,
    ) {
        $message = "Error capturing environment variable value: {$message} (" . $previous->getMessage() . ")";
        parent::__construct($message, ($code ?: 404), $previous);
    }
}
