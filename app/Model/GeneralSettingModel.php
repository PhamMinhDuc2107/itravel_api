<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSettingModel extends Model
{
    use HasFactory;

    protected $table = 'general_settings';

    public array $searchable = [];
    public array $sortable = ['id', 'created_at'];

    protected $fillable = [];

    protected $casts = [];
}

