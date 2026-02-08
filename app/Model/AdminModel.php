<?php

namespace App\Model;

use App\Enum\ActiveStateEnum;
use App\Support\File\DiskManager;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AdminModel extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admins';
    public array $searchable = ['email', 'name', 'phone'];
    public array $sortable = ['id', 'created_at', 'name', 'phone', 'status'];
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

    public function blogs()
    {
        return $this->hasMany(BlogModel::class, 'author_id');
    }

    public function isActive(): bool
    {
        return $this->status === ActiveStateEnum::Active;
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->attributes['avatar'] ?? null;

                $diskManager = $this->diskManager();

                return $diskManager->url($path);
            }
        );
    }

    private function diskManager(): DiskManager
    {
        return app(DiskManager::class);
    }
}
