<?php

namespace App\Http\Middlewares;

use App\Model\AdminModel;
use App\Repository\Contract\AdminRepositoryInterface;
use App\Support\Jwt\JwtSupport;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class AdminAuthMiddleware
{
    public function __construct(
        private readonly JwtSupport $jwt,
        private readonly AdminRepositoryInterface $adminRepository,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (! $token) {
            throw new AuthenticationException(
                __('auth.token_missing')
            );
        }

        try {
            $payload = $this->jwt->decode($token);
        } catch (\Throwable $e) {
            throw new AuthenticationException(
                __('auth.token_invalid')
            );
        }

        $admin = $this->adminRepository->find($payload['sub'] ?? null);

        if (! $admin) {
            throw new AuthenticationException(
                __('auth.user_not_found')
            );
        }

        auth()->setUser($admin);

        return $next($request);
    }
}
