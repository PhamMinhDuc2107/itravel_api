<?php

namespace App\Model;

use App\Enum\ActiveStateEnum;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class HotelModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hotels';

    public array $searchable = ['name', 'slug', 'excerpt', 'content', 'address'];
    public array $sortable = ['id', 'created_at', 'name', 'star_rating', 'price_from', 'status'];

    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'slug',
        'hotel_type_id',
        'location_id',
        'address',
        'latitude',
        'longitude',
        'image',
        'gallery',
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
        'gallery' => 'array',
        'star_rating' => 'integer',
        'price_from' => 'decimal:2',
        'is_featured' => 'integer',
        'view_count' => 'integer',
        'status' => ActiveStateEnum::class,
    ];

    public function hotelType(): BelongsTo
    {
        return $this->belongsTo(HotelTypeModel::class, 'hotel_type_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationModel::class, 'location_id');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(AmenityModel::class, 'hotel_amenity', 'hotel_id', 'amenity_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(HotelReviewModel::class, 'hotel_id');
    }

    public function bookingItems(): MorphMany
    {
        return $this->morphMany(BookingItemModel::class, 'productable');
    }

    public function consultations(): MorphMany
    {
        return $this->morphMany(ConsultationModel::class, 'productable');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                $path = $this->attributes['image'] ?? null;
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

