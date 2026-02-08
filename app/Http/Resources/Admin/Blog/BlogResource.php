<?php

namespace App\Http\Resources\Admin\Blog;

use App\Http\Resources\Admin\BlogCategory\BlogCategoryResource;
use App\Http\Resources\Base\BaseResource;

class BlogResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'image' => $this->image_url,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return new BlogCategoryResource($this->category);
            }),
            'author_id' => $this->author_id,
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                    'email' => $this->author->email,
                ];
            }),
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'view_count' => $this->view_count,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}

