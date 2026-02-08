<?php

namespace App\Model;

use App\Enum\ActiveStateEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelReviewModel extends Model
{
    use HasFactory;

    protected $table = 'hotel_reviews';

    public array $searchable = ['name', 'email', 'comment'];
    public array $sortable = ['id', 'created_at', 'rating', 'status'];

    protected $fillable = [
        'hotel_id',
        'user_id',
        'name',
        'email',
        'rating',
        'comment',
        'images',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'status' => ActiveStateEnum::class,
    ];

    public function hotel()
    {
        return $this->belongsTo(HotelModel::class, 'hotel_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

