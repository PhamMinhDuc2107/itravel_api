<?php

namespace App\Http\Resources\Admin\Hotel;

use App\Http\Resources\Base\BaseResourceCollection;

class HotelCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => HotelResource::collection($this->resource),
        ];
    }
}


