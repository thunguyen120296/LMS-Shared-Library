<?php

namespace Lms\Shared\Controller;

use Lms\Shared\Dto\ApiMeta;
use Lms\Shared\Dto\ApiResponse;
use Lms\Shared\Dto\FieldError;
use Lms\Shared\Http\ApiStatusCode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends AbstractController
{
    protected function success(
        mixed $data = null,
        string $message = 'Success',
        ?ApiMeta $meta = null,
        int $status = ApiStatusCode::OK,
        array $headers = [],
    ): JsonResponse {
        return new JsonResponse(ApiResponse::success($data, $message, $meta), $status, $headers);
    }

    /**
     * @param array<int, FieldError|array{field: string, message: string}>|null $errors
     */
    protected function error(
        string $message,
        ?array $errors = null,
        int $status = ApiStatusCode::BAD_REQUEST,
        array $headers = [],
    ): JsonResponse {
        return new JsonResponse(ApiResponse::error($message, $errors), $status, $headers);
    }

    protected function unauthorized(string $message = 'Unauthorized', array $headers = []): JsonResponse
    {
        return $this->error($message, status: ApiStatusCode::UNAUTHORIZED, headers: $headers);
    }

    protected function forbidden(string $message = 'Forbidden', array $headers = []): JsonResponse
    {
        return $this->error($message, status: ApiStatusCode::FORBIDDEN, headers: $headers);
    }

    protected function notFound(string $message = 'Not found', array $headers = []): JsonResponse
    {
        return $this->error($message, status: ApiStatusCode::NOT_FOUND, headers: $headers);
    }
}
