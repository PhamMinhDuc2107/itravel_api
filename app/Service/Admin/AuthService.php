<?php

namespace App\Service\Admin;

use App\Enum\ActiveStateEnum;
use App\Enum\AppErrorEnum;
use App\Enum\AuthUserTypeEnum;
use App\Exception\BusinessException;
use App\Model\AdminModel;
use App\Repository\Contract\AdminRepositoryInterface;
use App\Support\Jwt\JwtSupport;
use Illuminate\Auth\AuthenticationException;
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
            throw new AuthenticationException(
                __('auth.invalid_credentials'),
            );
        }

        if (! Hash::check($password, $admin->password)) {
            throw new AuthenticationException(
                __('auth.invalid_credentials'),
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

        $plainRefreshToken = $this->createRefreshToken($admin);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $plainRefreshToken,
            'token_type'    => 'Bearer',
            'expires_in'    => config('jwt.ttl'),
        ];
    }
    public function logout(AdminModel $admin): void
    {
        $admin->refreshTokens()->delete();

    }

    public function resetPassword(): void
    {

    }
    private function createRefreshToken(AdminModel $admin): string
    {
        $plainRefreshToken = Str::random(64);

        $admin->refreshTokens()
            ->where('auth_type', AuthUserTypeEnum::ADMIN)
            ->delete();

        $admin->refreshTokens()->create([
            'auth_type'  => AuthUserTypeEnum::ADMIN,
            'token_hash' => hash('sha256', $plainRefreshToken),
            'expired_at' => now()->addDays(7),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
        ]);
        return $plainRefreshToken;
    }
}
