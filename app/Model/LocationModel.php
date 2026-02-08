<?php

namespace App\Model;

use App\Enum\ActiveStateEnum;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'locations';
    protected $primaryKey = 'location_id';

    public array $searchable = ['name', 'slug', 'description', 'type'];
    public array $sortable = ['location_id', 'created_at', 'name', 'position', 'status', 'type'];

    protected $fillable = [
        'name',
        'slug',
        'parent_location_id',
        'description',
        'content',
        'image',
        'type',
        'display_home',
        'is_feature',
        'is_departure',
        'is_destination',
        'position',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'display_home' => 'integer',
        'is_feature' => 'integer',
        'is_departure' => 'integer',
        'is_destination' => 'integer',
        'position' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(LocationModel::class, 'parent_location_id', 'location_id');
    }

    public function children()
    {
        return $this->hasMany(LocationModel::class, 'parent_location_id', 'location_id');
    }

    public function toursAsDeparture()
    {
        return $this->hasMany(TourModel::class, 'departure_location_id', 'location_id');
    }

    public function toursAsDestination()
    {
        return $this->hasMany(TourModel::class, 'destination_location_id', 'location_id');
    }

    public function hotels()
    {
        return $this->hasMany(HotelModel::class, 'location_id', 'location_id');
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

