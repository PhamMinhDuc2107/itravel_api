<?php

namespace App\Http\Resources\Admin\Tour;

use App\Http\Resources\Base\BaseResourceCollection;

class TourCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => TourResource::collection($this->resource),
        ];
    }
}


