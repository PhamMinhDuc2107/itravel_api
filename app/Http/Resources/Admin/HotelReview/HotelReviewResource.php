<?php

namespace App\Http\Resources\Admin\HotelReview;

use App\Http\Resources\Base\BaseResource;

class HotelReviewResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'hotel_id' => $this->hotel_id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'images' => $this->images,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}


