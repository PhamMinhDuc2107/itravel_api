<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BookingItemModel extends Model
{
    use HasFactory;

    protected $table = 'booking_items';

    public array $searchable = ['product_name', 'product_code'];
    public array $sortable = ['id', 'created_at', 'start_date', 'total_price'];

    protected $fillable = [
        'booking_id',
        'productable_type',
        'productable_id',
        'product_name',
        'product_image',
        'product_code',
        'quantity',
        'price',
        'total_price',
        'start_date',
        'end_date',
        'options',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'options' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(BookingModel::class, 'booking_id');
    }

    public function productable(): MorphTo
    {
        return $this->morphTo();
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(BookingPassengerModel::class, 'booking_item_id');
    }
}

