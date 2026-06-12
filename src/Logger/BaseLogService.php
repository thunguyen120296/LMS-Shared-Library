<?php

namespace Lms\Shared\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class BaseLogService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ?string $service = null,
    ) {}

    public function for(string $service): self
    {
        return new self($this->logger, $service);
    }

    public function info(string $message, ?LogContext $context = null): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function warning(string $message, ?LogContext $context = null): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function error(string $message, ?\Throwable $e = null, ?LogContext $context = null): void
    {
        $extra = $context?->extra ?? [];
        if ($e !== null) {
            $extra['exception'] = $e::class;
            $extra['exception_message'] = $e->getMessage();
            $extra['trace'] = $e->getTraceAsString();
        }

        $this->log(LogLevel::ERROR, $message, new LogContext(
            service: $context?->service ?? $this->service,
            action: $context?->action,
            correlationId: $context?->correlationId,
            userId: $context?->userId,
            extra: $extra ?: null,
        ));
    }

    private function log(string $level, string $message, ?LogContext $context = null): void
    {
        $ctx = ($context ?? new LogContext(service: $this->service))->toArray();
        $this->logger->log($level, $message, $ctx);
    }
}
