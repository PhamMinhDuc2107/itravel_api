<?php

namespace App\Http\Resources\Admin\Amenity;

use App\Http\Resources\Base\BaseResource;

class AmenityResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'icon' => $this->icon_url,
            'type' => $this->type,
            'position' => $this->position,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}


