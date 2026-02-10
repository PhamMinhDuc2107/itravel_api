<?php

namespace App\Http\Resources\Admin\Amenity;

use App\Http\Resources\Base\BaseResourceCollection;

class AmenityCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => AmenityResource::collection($this->resource),
        ];
    }
}


