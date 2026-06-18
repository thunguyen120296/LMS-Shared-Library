<?php
namespace Lms\Shared\Service;

use Symfony\Component\HttpFoundation\RequestStack;

final class CorrelationIdService
{
    private ?string $correlationId = null;
    
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {}

    public function get(): string
    {
        if($this->correlationId !== null) {
            return $this->correlationId;
        }

        $request = $this->requestStack->getMainRequest();
        $headerId = $request->headers->get('X-Correlation-ID');
        $this->correlationId = $headerId ?? bin2hex(random_bytes(16));
        return $this->correlationId;
    }
}