<?php

namespace App\Model;

use App\Enum\ConsultationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'consultations';

    public array $searchable = ['name', 'phone', 'email', 'message', 'product_name'];
    public array $sortable = ['id', 'created_at', 'status'];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'productable_type',
        'productable_id',
        'product_name',
        'metadata',
        'message',
        'status',
        'user_id',
        'staff_notes',
    ];

    protected $casts = [
        'metadata' => 'array',
        'status' => ConsultationStatusEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productable(): MorphTo
    {
        return $this->morphTo();
    }
}

