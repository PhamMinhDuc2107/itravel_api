<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategoryModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blog_categories';

    public array $searchable = ['name', 'slug', 'description'];
    public array $sortable = ['id', 'created_at', 'name', 'position', 'status'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'position',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function blogs()
    {
        return $this->hasMany(BlogModel::class, 'category_id');
    }
}

