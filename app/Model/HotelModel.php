<?php

namespace App\Model;

use App\Enum\ActiveStateEnum;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hotels';

    public array $searchable = ['name', 'slug', 'excerpt', 'content', 'address'];
    public array $sortable = ['id', 'created_at', 'name', 'star_rating', 'price_from', 'status'];

    protected $fillable = [
        'name',
        'slug',
        'hotel_type_id',
        'location_id',
        'address',
        'latitude',
        'longitude',
        'star_rating',
        'price_from',
        'excerpt',
        'content',
        'policies',
        'email',
        'phone',
        'website',
        'check_in_time',
        'check_out_time',
        'is_featured',
        'view_count',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'star_rating' => 'integer',
        'price_from' => 'decimal:2',
        'is_featured' => 'integer',
        'view_count' => 'integer',
        'status' => ActiveStateEnum::class,
    ];

    public function hotelType()
    {
        return $this->belongsTo(HotelTypeModel::class, 'hotel_type_id');
    }

    public function location()
    {
        return $this->belongsTo(LocationModel::class, 'location_id');
    }

    public function amenities()
    {
        return $this->belongsToMany(AmenityModel::class, 'hotel_amenity', 'hotel_id', 'amenity_id');
    }

    public function reviews()
    {
        return $this->hasMany(HotelReviewModel::class, 'hotel_id');
    }

    public function bookingItems()
    {
        return $this->morphMany(BookingItemModel::class, 'productable');
    }

    public function consultations()
    {
        return $this->morphMany(ConsultationModel::class, 'productable');
    }
}

