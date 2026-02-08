<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategoryModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blog_categories';
    protected $primaryKey = 'blog_category_id';

    public array $searchable = ['name', 'slug', 'description'];
    public array $sortable = ['blog_category_id', 'created_at', 'name', 'position', 'status'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'position',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function blogs()
    {
        return $this->hasMany(BlogModel::class, 'blog_category_id', 'blog_category_id');
    }
}

