<?php

namespace App\Http\Resources\Admin\Tour;

use App\Http\Resources\Admin\Category\CategoryResource;
use App\Http\Resources\Admin\Location\LocationResource;
use App\Http\Resources\Base\BaseResource;

class TourResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'slug' => $this->slug,

            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),

            'departure_location_id' => $this->departure_location_id,
            'departure_location' => $this->whenLoaded('departureLocation', function () {
                return new LocationResource($this->departureLocation);
            }),

            'destination_location_id' => $this->destination_location_id,
            'destination_location' => $this->whenLoaded('destinationLocation', function () {
                return new LocationResource($this->destinationLocation);
            }),

            'duration_days' => $this->duration_days,
            'duration_nights' => $this->duration_nights,

            'is_recurring' => $this->is_recurring,
            'recurring_days' => $this->recurring_days,

            'price_adult' => $this->price_adult,
            'price_child' => $this->price_child,
            'price_infant' => $this->price_infant,

            'excerpt' => $this->excerpt,
            'overview' => $this->overview,
            'policy' => $this->policy,
            'included' => $this->included,
            'excluded' => $this->excluded,

            'image' => $this->image_url,
            'gallery' => $this->gallery,

            'view_count' => $this->view_count,
            'position' => $this->position,
            'status' => $this->status,

            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}


