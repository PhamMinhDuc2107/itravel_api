<?php

namespace App\Http\Resources\Admin\Category;

use App\Http\Resources\Base\BaseResource;

class CategoryResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', function () {
                return new CategoryResource($this->parent);
            }),
            'description' => $this->description,
            'position' => $this->position,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

