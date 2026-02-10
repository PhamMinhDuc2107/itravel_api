<?php

namespace App\Http\Resources\Admin\HotelReview;

use App\Http\Resources\Base\BaseResourceCollection;

class HotelReviewCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => HotelReviewResource::collection($this->resource),
        ];
    }
}


