<?php

namespace App\Model;

use App\Enum\PassengerTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPassengerModel extends Model
{
    use HasFactory;

    protected $table = 'booking_passengers';

    public array $searchable = ['full_name', 'phone', 'passport_number'];
    public array $sortable = ['id', 'created_at', 'full_name', 'type'];

    protected $fillable = [
        'booking_item_id',
        'full_name',
        'dob',
        'gender',
        'phone',
        'passport_number',
        'passport_expiry',
        'nationality',
        'type',
    ];

    protected $casts = [
        'dob' => 'date',
        'passport_expiry' => 'date',
        'type' => PassengerTypeEnum::class,
    ];

    public function bookingItem(): BelongsTo
    {
        return $this->belongsTo(BookingItemModel::class, 'booking_item_id');
    }
}

