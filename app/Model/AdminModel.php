<?php

namespace App\Model;

use App\Enum\ActiveStateEnum;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use App\Model\RefreshTokenModel;

class AdminModel extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'email',
        'name',
        'password',
        'phone',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => ActiveStateEnum::class,
    ];


    public function refreshTokens(): MorphMany
    {
        return $this->morphMany(RefreshTokenModel::class, 'tokenable');
    }


    public function isActive(): bool
    {
        return $this->status === ActiveStateEnum::Active;
    }
}
