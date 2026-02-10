<?php

namespace App\Model;

use App\Enum\BookingStatusEnum;
use App\Enum\PaymentStatusEnum;
use App\Enum\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bookings';

    public array $searchable = ['code', 'customer_name', 'customer_phone', 'customer_email'];
    public array $sortable = ['id', 'created_at', 'code', 'status', 'payment_status', 'total_amount'];

    protected $fillable = [
        'code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'note',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'source',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'status' => BookingStatusEnum::class,
        'payment_status' => PaymentStatusEnum::class,
        'payment_method' => PaymentMethodEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItemModel::class, 'booking_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BookingLogModel::class, 'booking_id');
    }
}

