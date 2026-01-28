<?php

namespace App\Services\Admin;

use App\Enums\ActiveStateEnum;
use App\Enums\AppErrorEnum;
use App\Enums\AuthUserTypeEnum;
use App\Exceptions\BusinessException;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Support\Jwt\JwtSupport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        private readonly AdminRepositoryInterface $adminRepository,
        private readonly JwtSupport $jwt
    ) {}


    public function login(string $email, string $password): array
    {
        $admin = $this->adminRepository->getAdminByEmail($email);

        if (! $admin) {
            throw new BusinessException(
                __('auth.invalid_credentials'),
                AppErrorEnum::UNAUTHORIZED->value
            );
        }

        if (! Hash::check($password, $admin->password)) {
            throw new BusinessException(
                __('auth.invalid_credentials'),
                AppErrorEnum::UNAUTHORIZED->value
            );
        }

        if ($admin->status !== ActiveStateEnum::Active) {
            throw new BusinessException(
                __('auth.account_locked'),
                AppErrorEnum::UNAUTHORIZED->value
            );
        }


        $accessToken = $this->jwt->encode([
            'sub'       => $admin->id,
            'auth_type' => AuthUserTypeEnum::ADMIN->value,
        ]);

        $plainRefreshToken = Str::random(64);

        $admin->refreshTokens()->create([
            'auth_type'  => AuthUserTypeEnum::ADMIN,
            'token_hash' => hash('sha256', $plainRefreshToken),
            'expired_at' => now()->addDays(7),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
        ]);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $plainRefreshToken,
            'token_type'    => 'Bearer',
            'expires_in'    => config('jwt.ttl'),
        ];
    }
}
