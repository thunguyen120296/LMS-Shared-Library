<?php

namespace Lms\Shared\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

trait BaseLogTrait
{
    private LoggerInterface $logger;

    /** @required */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    protected function logInfo(string $message, ?LogContext $context = null): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    protected function logWarning(string $message, ?LogContext $context = null): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    protected function logError(string $message, ?\Throwable $e = null, ?LogContext $context = null): void
    {
        $extra = $context?->extra ?? [];
        if ($e !== null) {
            $extra['exception'] = $e::class;
            $extra['exception_message'] = $e->getMessage();
            $extra['trace'] = $e->getTraceAsString();
        }

        $this->log(LogLevel::ERROR, $message, new LogContext(
            service: $context?->service ?? static::class,
            action: $context?->action,
            correlationId: $context?->correlationId,
            userId: $context?->userId,
            extra: $extra ?: null,
        ));
    }

    private function log(string $level, string $message, ?LogContext $context = null): void
    {
        $ctx = ($context ?? new LogContext(service: static::class))->toArray();
        $this->logger->log($level, $message, $ctx);
    }
}