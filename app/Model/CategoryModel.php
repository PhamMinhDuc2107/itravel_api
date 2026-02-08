<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryModel extends Model
{
    use SoftDeletes;

    protected $table = 'categories';
    
    public array $searchable = ['name', 'slug', 'description'];
    public array $sortable = ['id', 'created_at', 'name', 'position', 'status'];
    
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'name',
        'slug',
        'parent_category_id',
        'description',
        'position',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'position' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(CategoryModel::class, 'parent_category_id', 'category_id');
    }

    public function children()
    {
        return $this->hasMany(CategoryModel::class, 'parent_category_id', 'category_id');
    }

    public function tours()
    {
        return $this->hasMany(TourModel::class, 'category_id', 'category_id');
    }
}

