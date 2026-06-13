<?php

namespace Lms\Shared\Exception;

use Lms\Shared\Dto\FieldError;
use Lms\Shared\Http\ApiStatusCode;

class ApiException extends \RuntimeException
{
    /**
     * @param array<int, FieldError|array{field: string, message: string}>|null $errors
     */
    public function __construct(
        string $message,
        private readonly int $statusCode = ApiStatusCode::BAD_REQUEST,
        private readonly ?array $errors = null,
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<int, FieldError|array{field: string, message: string}>|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
