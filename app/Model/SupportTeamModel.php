<?php

namespace App\Model;

use App\Enum\SupportTeamGroupEnum;
use App\Enum\SupportTeamRoleEnum;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SupportTeamModel extends Model
{
    use HasFactory;

    protected $table = 'support_team';

    public array $searchable = ['name', 'phone', 'zalo'];
    public array $sortable = ['id', 'created_at', 'name', 'position', 'status'];

    protected $fillable = [
        'name',
        'phone',
        'zalo',
        'avatar',
        'role',
        'group',
        'position',
        'status',
    ];

    protected $casts = [
        'position' => 'integer',
        'status' => 'integer',
        'role' => SupportTeamRoleEnum::class,
        'group' => SupportTeamGroupEnum::class,
    ];

    protected $appends = [
        'avatar_url',
    ];

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->attributes['avatar'] ?? null;
                if (!$path) {
                    return null;
                }
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

