<?php

namespace App\Http\Resources\Admin\Location;

use App\Http\Resources\Base\BaseResource;

class LocationResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->location_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_location_id' => $this->parent_location_id,
            'parent' => $this->whenLoaded('parent', function () {
                return new LocationResource($this->parent);
            }),
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->image_url,
            'type' => $this->type,
            'display_home' => $this->display_home,
            'is_feature' => $this->is_feature,
            'is_departure' => $this->is_departure,
            'is_destination' => $this->is_destination,
            'position' => $this->position,
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

