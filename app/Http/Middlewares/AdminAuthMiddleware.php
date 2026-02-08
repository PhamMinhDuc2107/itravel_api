<?php

namespace App\Http\Middlewares;

use App\Repository\Contract\AdminRepositoryInterface;
use App\Support\Jwt\JwtSupport;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

readonly class AdminAuthMiddleware
{
    public function __construct(
        private JwtSupport               $jwt,
        private AdminRepositoryInterface $adminRepository,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (! $token) {
            throw new AuthenticationException(
                __('auth.token_missing')
            );
        }

        $payload = $this->jwt->decode($token);

        $admin = $this->adminRepository->find($payload['sub'] ?? null);

        if (! $admin) {
            throw new AuthenticationException(
                __('auth.user_not_found')
            );
        }

        auth()->setUser($admin);
        $request->attributes->set('payload', $payload);
        return $next($request);
    }
}
