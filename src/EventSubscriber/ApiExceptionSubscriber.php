<?php

namespace Lms\Shared\EventSubscriber;

use Lms\Shared\Dto\ApiResponse;
use Lms\Shared\Exception\ApiException;
use Lms\Shared\Http\ApiStatusCode;
use Lms\Shared\Logger\BaseLogService;
use Lms\Shared\Logger\LogContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly bool $debug = false,
        private readonly ?BaseLogService $logger = null,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->isApiRequest($event->getRequest())) {
            return;
        }

        $exception = $event->getThrowable();
        $statusCode = $this->resolveStatusCode($exception);

        $event->setResponse(new JsonResponse(
            $this->buildPayload($exception, $statusCode),
            $statusCode,
        ));

        if ($statusCode >= ApiStatusCode::INTERNAL_SERVER_ERROR && $this->logger !== null) {
            $this->logger->for(self::class)->error(
                $exception->getMessage(),
                $exception,
                new LogContext(
                    action: 'api.exception',
                    extra: ['path' => $event->getRequest()->getPathInfo()],
                ),
            );
        }
    }

    private function isApiRequest(Request $request): bool
    {
        if (str_starts_with($request->getPathInfo(), '/api')) {
            return true;
        }

        $accept = $request->headers->get('Accept', '');

        return str_contains($accept, 'application/json')
            || $request->getContentTypeFormat() === 'json'
            || $request->getPreferredFormat() === 'json';
    }

    private function resolveStatusCode(\Throwable $exception): int
    {
        if ($exception instanceof ApiException) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        return ApiStatusCode::INTERNAL_SERVER_ERROR;
    }

    private function buildPayload(\Throwable $exception, int $statusCode): array
    {
        $errors = $exception instanceof ApiException ? $exception->getErrors() : null;
        $debug = null;

        if ($this->debug) {
            $debug = [
                'exception' => $exception::class,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        return ApiResponse::error(
            $this->resolveMessage($exception, $statusCode),
            $errors,
            $debug,
        );
    }

    private function resolveMessage(\Throwable $exception, int $statusCode): string
    {
        if ($statusCode >= ApiStatusCode::INTERNAL_SERVER_ERROR && !$this->debug) {
            return 'Internal Server Error';
        }

        return $exception->getMessage() ?: 'An error occurred';
    }
}
