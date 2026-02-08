<?php

namespace App\Model;

use App\Enum\ActiveStateEnum;
use App\Enum\AmenityTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function hotels()
    {
        return $this->belongsToMany(HotelModel::class, 'hotel_amenity', 'amenity_id', 'hotel_id');
    }
}

