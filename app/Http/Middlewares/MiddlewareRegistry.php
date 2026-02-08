<?php

namespace App\Http\Middlewares;

class MiddlewareRegistry
{
    public static function alias(): array
    {
        return [
            'admin.auth' => AdminAuthMiddleware::class,
        ];
    }

    public static function global(): array
    {
        return [
            // Global middleware
            // TrustHostMiddleware::class,
        ];
    }

    public static function api(): array
    {
        return [
            SetupContextMiddleware::class,
        ];
    }

    public static function web(): array
    {
        return [
            // Web only
        ];
    }
}
