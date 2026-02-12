<?php

namespace App\Service\Admin;

use App\Enum\ActiveStateEnum;
use App\Enum\AppErrorEnum;
use App\Enum\AuthUserTypeEnum;
use App\Enum\RedisKeyEnum;
use App\Exception\BusinessException;
use App\Model\AdminModel;
use App\Repository\Contract\AdminRepositoryInterface;
use App\Support\Jwt\JwtSupport;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

readonly class AuthService
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private JwtSupport               $jwt
    ) {}

    public function login(string $email, string $password): array
    {
        $admin = $this->adminRepository->getAdminByEmail($email);

        if (! $admin || ! Hash::check($password, $admin->password)) {
            throw new AuthenticationException(__('auth.invalid_credentials'));
        }

        if ($admin->status !== ActiveStateEnum::Active) {
            throw new BusinessException(
                __('auth.account_locked'),
                AppErrorEnum::UNAUTHORIZED->value
            );
        }
        $payload = [
            'sub'       => $admin->id,
            'auth_type' => AuthUserTypeEnum::ADMIN->value,
        ];

        $accessToken = $this->jwt->encode($payload);


        $plainRefreshToken = $this->createRefreshToken($admin);

        return [
            'user'          => $admin,
            'access_token'  => $accessToken,
            'refresh_token' => $plainRefreshToken,
            'token_type'    => 'Bearer',
            'expires_in'    => config('jwt.ttl'),
        ];
    }
    public function logout(AdminModel $admin, array $payload): void
    {
        $admin->refreshTokens()->delete();
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
