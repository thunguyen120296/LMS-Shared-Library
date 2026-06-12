<?php

namespace Lms\Shared\Dto;

final class ApiResponse
{
    public static function success(mixed $data = null): array
    {
        return [
            'status' => 'success',
            'data' => $data,
        ];
    }

    public static function error(string $message, ?array $errors = null, ?array $debug = null): array
    {
        $payload = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        if ($debug !== null) {
            $payload['debug'] = $debug;
        }

        return $payload;
    }
}
