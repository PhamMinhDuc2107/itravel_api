<?php

namespace App\Support\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;
use Throwable;

class JwtSupport
{
    /**
     * Encode access token
     */
    public function encode(array $payload, ?int $ttl = null): string
    {
        $now = Carbon::now()->timestamp;

        $payload = array_merge($payload, [
            'iat' => $now,
            'exp' => $now + ($ttl ?? config('jwt.ttl')),
        ]);

        return JWT::encode(
            $payload,
            config('jwt.secret'),
            config('jwt.alg')
        );
    }

    /**
     * Encode refresh token
     */
    public function encodeRefresh(array $payload): string
    {
        return $this->encode($payload, config('jwt.refresh_ttl'));
    }

    /**
     * Decode token
     */
    public function decode(string $token): array
    {
        try {
            return (array) JWT::decode(
                $token,
                new Key(config('jwt.secret'), config('jwt.alg'))
            );
        } catch (Throwable $e) {
            throw new \RuntimeException('Invalid or expired token', 401);
        }
    }
}
