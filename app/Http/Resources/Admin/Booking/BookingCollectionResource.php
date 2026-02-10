<?php

namespace App\Http\Resources\Admin\Booking;

use App\Http\Resources\Base\BaseResourceCollection;

class BookingCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => BookingResource::collection($this->resource),
        ];
    }
}

