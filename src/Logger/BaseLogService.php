<?php

namespace Lms\Shared\Logger;

use Lms\Shared\Service\CorrelationIdService;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class BaseLogService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ?string $service = null,
        private readonly CorrelationIdService $correlationIdService,
    ) {}

    public function for(string $service): self
    {
        return new self($this->logger, $service, $this->correlationIdService);
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

    public function createTraceableRequest(string $method, string $url, array $options = []){
        $options['headers']['X-Correlation-ID'] = $this->correlationIdService->get();
        return $options;
    }

    private function log(string $level, string $message, ?LogContext $context = null): void
    {
        $ctx = ($context ?? new LogContext(service: $this->service))->toArray();
        if(!isset($ctx['correlation_id'])) {
            $ctx['correlation_id'] = $this->correlationIdService->get();
        }
        $this->logger->log($level, $message, $ctx);
    }
}
