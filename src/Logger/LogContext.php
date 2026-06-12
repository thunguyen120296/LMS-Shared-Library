<?php

namespace Lms\Shared\Logger;

final class LogContext
{
    public function __construct(
        public readonly ?string $service = null,
        public readonly ?string $action = null,
        public readonly ?string $correlationId = null,
        public readonly ?string $userId = null,
        public readonly ?array $extra = null,
    ){}

    public function toArray(): array
    {
        return array_filter([
            'service' => $this->service,
            'action' => $this->action,
            'correlation_id' => $this->correlationId,
            'user_id' => $this->userId,
            ...($this->extra ?? []),
        ], fn ($v) => $v !== null);
    }
}