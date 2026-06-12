<?php

namespace Lms\Shared\Controller;

use Lms\Shared\Dto\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends AbstractController
{
    protected function success(mixed $data = null, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse(ApiResponse::success($data), $status, $headers);
    }

    protected function error(string $message, int $status = 400, array $headers = []): JsonResponse
    {
        return new JsonResponse(ApiResponse::error($message), $status, $headers);
    }
}