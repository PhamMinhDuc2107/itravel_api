<?php

namespace App\Http\Resources\Admin\Booking;

use App\Http\Resources\Base\BaseResource;

class BookingResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user'),
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'note' => $this->note,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'source' => $this->source,
            'items' => $this->whenLoaded('items', function () {
                return BookingItemResource::collection($this->items);
            }),
            'logs' => $this->whenLoaded('logs', function () {
                return BookingLogResource::collection($this->logs);
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}

