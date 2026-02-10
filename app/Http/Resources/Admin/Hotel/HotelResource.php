<?php

namespace App\Http\Resources\Admin\Hotel;

use App\Http\Resources\Admin\Amenity\AmenityResource;
use App\Http\Resources\Admin\Location\LocationResource;
use App\Http\Resources\Base\BaseResource;

class HotelResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'hotel_type_id' => $this->hotel_type_id,
            'location_id' => $this->location_id,
            'location' => $this->whenLoaded('location', function () {
                return new LocationResource($this->location);
            }),
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'image' => $this->image_url,
            'gallery' => $this->gallery,
            'star_rating' => $this->star_rating,
            'price_from' => $this->price_from,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'policies' => $this->policies,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'is_featured' => $this->is_featured,
            'view_count' => $this->view_count,
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'amenities' => $this->whenLoaded('amenities', function () {
                return AmenityResource::collection($this->amenities);
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}


