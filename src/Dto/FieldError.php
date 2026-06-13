<?php

namespace Lms\Shared\Dto;

final class FieldError
{
    public function __construct(
        public readonly string $field,
        public readonly string $message,
    ) {}

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'message' => $this->message,
        ];
    }
}
