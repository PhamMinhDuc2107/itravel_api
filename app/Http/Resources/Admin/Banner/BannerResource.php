<?php

namespace App\Http\Resources\Admin\Banner;

use App\Http\Resources\Base\BaseResource;

class BannerResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image_url,
            'mobile_image' => $this->mobile_image_url,
            'link' => $this->link,
            'target' => $this->target,
            'description' => $this->description,
            'type' => $this->type,
            'position' => $this->position,
            'status' => $this->status,
            'start_at' => $this->start_at?->format('Y-m-d H:i:s'),
            'end_at' => $this->end_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}

