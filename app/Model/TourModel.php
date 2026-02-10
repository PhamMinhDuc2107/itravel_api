<?php

namespace App\Model;

use App\Enum\TourStatusEnum;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TourModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tours';

    public array $searchable = ['name', 'slug', 'code', 'excerpt', 'overview'];
    public array $sortable = ['id', 'created_at', 'name', 'position', 'status', 'price_adult'];

    protected $fillable = [
        'code',
        'name',
        'slug',
        'category_id',
        'departure_location_id',
        'destination_location_id',
        'duration_days',
        'duration_nights',
        'is_recurring',
        'recurring_days',
        'price_adult',
        'price_child',
        'price_infant',
        'excerpt',
        'overview',
        'policy',
        'included',
        'excluded',
        'image',
        'gallery',
        'view_count',
        'position',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'duration_nights' => 'integer',
        'is_recurring' => 'integer',
        'recurring_days' => 'array',
        'price_adult' => 'decimal:2',
        'price_child' => 'decimal:2',
        'price_infant' => 'decimal:2',
        'gallery' => 'array',
        'view_count' => 'integer',
        'position' => 'integer',
        'status' => TourStatusEnum::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function departureLocation(): BelongsTo
    {
        return $this->belongsTo(LocationModel::class, 'departure_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(LocationModel::class, 'destination_location_id');
    }

    public function departures(): HasMany
    {
        return $this->hasMany(TourDepartureModel::class, 'tour_id');
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(TourItineraryModel::class, 'tour_id');
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

