<?php

use App\Exception\ApiExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php'
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias(\App\Http\Middlewares\MiddlewareRegistry::alias());
        $middleware->api(\App\Http\Middlewares\SetupContextMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
        ApiExceptionHandler::register($exceptions);
    })->create();
