<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourItineraryModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tour_itineraries';

    public array $searchable = ['title', 'content'];
    public array $sortable = ['id', 'created_at', 'day_number', 'position'];

    protected $fillable = [
        'tour_id',
        'day_number',
        'position',
        'title',
        'content',
    ];

    protected $casts = [
        'day_number' => 'integer',
        'position' => 'integer',
    ];

    public function tour()
    {
        return $this->belongsTo(TourModel::class, 'tour_id');
    }
}

