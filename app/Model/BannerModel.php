<?php

namespace App\Model;

use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class BannerModel extends Model
{
    use SoftDeletes;

    protected $table = 'banners';
    
    public array $searchable = ['name', 'description', 'type'];
    public array $sortable = ['id', 'created_at', 'name', 'position', 'status', 'type'];
    
    protected $fillable = [
        'name',
        'image',
        'mobile_image',
        'link',
        'target',
        'description',
        'type',
        'position',
        'status',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'status' => 'integer',
        'position' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->attributes['image'] ?? null;
                $diskManager = $this->diskManager();
                return $diskManager->url($path);
            }
        );
    }

    protected function mobileImageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->attributes['mobile_image'] ?? null;
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

