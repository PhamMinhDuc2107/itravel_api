<?php

namespace App\Http\Middlewares;

class MiddlewareRegistry
{
    public static function alias(): array
    {
        return [
            'admin.auth' => \App\Http\Middlewares\AdminAuthMiddleware::class,
        ];
    }
}
