<?php

namespace App\Model;

use App\Enum\TourDepartureStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourDepartureModel extends Model
{
    use HasFactory;

    protected $table = 'tour_departures';

    public array $searchable = [];
    public array $sortable = ['id', 'created_at', 'start_date', 'status'];

    protected $fillable = [
        'tour_id',
        'start_date',
        'price_adult',
        'original_price_adult',
        'price_child',
        'original_price_child',
        'price_infant',
        'original_price_infant',
        'stock',
        'booked',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'price_adult' => 'decimal:2',
        'original_price_adult' => 'decimal:2',
        'price_child' => 'decimal:2',
        'original_price_child' => 'decimal:2',
        'price_infant' => 'decimal:2',
        'original_price_infant' => 'decimal:2',
        'stock' => 'integer',
        'booked' => 'integer',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(TourModel::class, 'tour_id');
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->booked);
    }
}

