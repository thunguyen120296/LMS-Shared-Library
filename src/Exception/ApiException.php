<?php

namespace Lms\Shared\Exception;

class ApiException extends \RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 400,
        private readonly ?array $errors = null,
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
