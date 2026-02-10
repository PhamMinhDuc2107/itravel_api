<?php

namespace App\Model;

use App\Enum\ActiveStateEnum;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AmenityModel extends Model
{
    use HasFactory;

    protected $table = 'amenities';

    public array $searchable = ['name', 'code'];
    public array $sortable = ['id', 'created_at', 'name', 'position', 'status'];

    protected $fillable = [
        'name',
        'code',
        'icon',
        'type',
        'position',
        'status',
    ];

    protected $casts = [
        'position' => 'integer',
        'status' => ActiveStateEnum::class,
    ];

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(HotelModel::class, 'hotel_amenity', 'amenity_id', 'hotel_id');
    }

    protected function iconUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                $path = $this->attributes['icon'] ?? null;
                if (! $path) {
                    return null;
                }

                return $this->diskManager()->url($path);
            }
        );
    }

    private function diskManager(): DiskManager
    {
        return app(DiskManager::class);
    }
}

