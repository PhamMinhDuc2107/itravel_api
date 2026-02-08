<?php

namespace App\Model;

use App\Enum\BlogStatusEnum;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blogs';

    public array $searchable = ['name', 'slug', 'excerpt', 'content'];
    public array $sortable = ['id', 'created_at', 'name', 'view_count', 'published_at', 'status'];

    protected $fillable = [
        'name',
        'slug',
        'excerpt',
        'content',
        'image',
        'category_id',
        'author_id',
        'status',
        'is_featured',
        'view_count',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_featured' => 'integer',
        'view_count' => 'integer',
        'published_at' => 'datetime',
        'status' => BlogStatusEnum::class,
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategoryModel::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(AdminModel::class, 'author_id');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->attributes['image'] ?? null;
                if (!$path) {
                    return null;
                }
                $diskManager = $this->diskManager();
                return $diskManager->url($path);
            }
        );
    }

    private function diskManager(): DiskManager
    {
        return app(DiskManager::class);
    }
}

