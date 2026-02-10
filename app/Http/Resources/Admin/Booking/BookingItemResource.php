<?php

namespace App\Http\Resources\Admin\Booking;

use App\Http\Resources\Base\BaseResource;

class BookingItemResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'productable_type' => $this->productable_type,
            'productable_id' => $this->productable_id,
            'productable' => $this->whenLoaded('productable'),
            'product_name' => $this->product_name,
            'product_image' => $this->product_image,
            'product_code' => $this->product_code,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total_price' => $this->total_price,
            'start_date' => $this->start_date?->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date?->format('Y-m-d H:i:s'),
            'options' => $this->options,
            'passengers' => $this->whenLoaded('passengers', function () {
                return BookingPassengerResource::collection($this->passengers);
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

