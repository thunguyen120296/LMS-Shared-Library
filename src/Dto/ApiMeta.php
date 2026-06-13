<?php

namespace Lms\Shared\Dto;

final class ApiMeta
{
    public function __construct(
        public readonly int $page,
        public readonly int $size,
        public readonly int $totalItems,
        public readonly int $totalPages,
    ) {}

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'size' => $this->size,
            'totalItems' => $this->totalItems,
            'totalPages' => $this->totalPages,
        ];
    }
}
