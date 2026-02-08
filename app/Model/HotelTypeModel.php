<?php

namespace App\Model;

use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class HotelTypeModel extends Model
{
    use HasFactory;

    protected $table = 'hotel_types';

    public array $searchable = ['name', 'slug'];
    public array $sortable = ['id', 'created_at', 'name', 'position', 'status'];

    protected $fillable = [
        'name',
        'slug',
        'image',
        'position',
        'status',
    ];

    protected $casts = [
        'position' => 'integer',
        'status' => 'integer',
    ];

    public function hotels()
    {
        return $this->hasMany(HotelModel::class, 'hotel_type_id');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->attributes['image'] ?? null;
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

