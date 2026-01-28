<?php

namespace App\Models;

use App\Enums\AuthUserTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RefreshTokenModel extends Model
{
    protected $table = 'refresh_tokens';

    protected $fillable = [
        'auth_type',
        'token_hash',
        'expired_at',
        'revoked_at',
        'user_agent',
        'ip_address',
    ];

    protected $casts = [
        'auth_type'  => AuthUserTypeEnum::class,
        'expired_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];


    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }


    public function isValid(): bool
    {
        return is_null($this->revoked_at)
            && $this->expired_at->isFuture();
    }


    public function revoke(): void
    {
        $this->update([
            'revoked_at' => now(),
        ]);
    }
}
