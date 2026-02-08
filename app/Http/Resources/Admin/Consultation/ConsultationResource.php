<?php

namespace App\Http\Resources\Admin\Consultation;

use App\Http\Resources\Base\BaseResource;

class ConsultationResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'productable_type' => $this->productable_type,
            'productable_id' => $this->productable_id,
            'productable' => $this->whenLoaded('productable', function () {
                return $this->productable;
            }),
            'product_name' => $this->product_name,
            'metadata' => $this->metadata,
            'message' => $this->message,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'staff_notes' => $this->staff_notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}

