<?php

namespace App\Http\Resources\Admin\Location;

use App\Http\Resources\Base\BaseResourceCollection;

class LocationCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => LocationResource::collection($this->resource),
        ];
    }
}

