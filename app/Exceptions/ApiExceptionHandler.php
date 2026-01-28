<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Configuration\Exceptions;
use Throwable;

use App\Exceptions\BusinessException;
use App\Exceptions\NotFoundException;
use App\Enums\AppErrorEnum;
use App\Context\AppContext;

class ApiExceptionHandler
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->render(fn(BusinessException $e) => self::handleBusiness($e));

        $exceptions->render(fn(ValidationException $e, Request $request) => self::handleValidation($e, $request));

        $exceptions->render(fn(ModelNotFoundException $e, Request $request) => self::handleModelNotFound($e, $request));

        $exceptions->render(fn(NotFoundHttpException $e, Request $request) => self::handleNotFoundHttp($request));

        $exceptions->render(fn(MethodNotAllowedHttpException $e, Request $request) => self::handleMethodNotAllowed($request));

        $exceptions->render(fn(Throwable $e, Request $request) => self::handleSystemError($e, $request));
    }


    private static function handleBusiness(BusinessException $e): JsonResponse
    {
        return self::jsonResponse(
            $e->getCode() ?: 400,
            $e->getErrorEnum(),
            $e->getMessage()
        );
    }

    private static function handleValidation(ValidationException $e, Request $request): ?JsonResponse
    {
        if (!$request->is('api/*')) return null;

        return self::jsonResponse(
            422,
            AppErrorEnum::VALIDATION_ERROR,
            null,
            $e->errors()
        );
    }

    private static function handleModelNotFound(ModelNotFoundException $e, Request $request): void
    {
        if ($request->is('api/*')) {
            $modelName = class_basename($e->getModel());
            $ids = implode(', ', $e->getIds());
            throw new NotFoundException($modelName, $ids);
        }
    }

    private static function handleNotFoundHttp(Request $request): ?JsonResponse
    {
        if (!$request->is('api/*')) return null;
        return self::jsonResponse(404, AppErrorEnum::NOT_FOUND);
    }

    private static function handleMethodNotAllowed(Request $request): ?JsonResponse
    {
        if (!$request->is('api/*')) return null;
        return self::jsonResponse(405, AppErrorEnum::BAD_REQUEST, 'Method not allowed.');
    }

    private static function handleSystemError(Throwable $e, Request $request): ?JsonResponse
    {
        if (!$request->is('api/*')) return null;

        Log::error('[SYSTEM_ERROR] ' . $e->getMessage(), [
            'request_id' => AppContext::getRequestId(),
            'user_id'    => AppContext::getUserId(),
            'url'        => $request->fullUrl(),
            'method'     => $request->method(),
            'trace'      => $e->getTraceAsString(),
        ]);

        $message = app()->hasDebugModeEnabled() ? $e->getMessage() : null;

        return self::jsonResponse(500, AppErrorEnum::SERVER_ERROR, $message);
    }


    private static function jsonResponse(
        int $statusCode,
        AppErrorEnum $errorEnum,
        ?string $message = null,
        $errors = null
    ): JsonResponse {
        return response()->json([
            'data' => [
                'status'      => $errorEnum->value,
                'status_code' => $statusCode,
                'message'     => $message ?? $errorEnum->message(),
                'errors'      => $errors,
            ],
            'meta' => [
                'timestamp'  => now()->toIso8601String(),
                'request_id' => AppContext::getRequestId(),
            ]
        ], $statusCode);
    }
}
