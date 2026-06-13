<?php

namespace Lms\Shared\Dto;

final class ApiResponse
{
    /**
     * @param array<int, FieldError|array{field: string, message: string}>|null $errors
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        ?ApiMeta $meta = null,
    ): array {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if ($meta !== null) {
            $payload['meta'] = $meta->toArray();
        }

        return $payload;
    }

    /**
     * @param array<int, FieldError|array{field: string, message: string}>|null $errors
     */
    public static function error(
        string $message,
        ?array $errors = null,
        ?array $debug = null,
    ): array {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = array_map(
                fn (FieldError|array $error) => $error instanceof FieldError ? $error->toArray() : $error,
                $errors,
            );
        }

        if ($debug !== null) {
            $payload['debug'] = $debug;
        }

        return $payload;
    }
}
