<?php

namespace App\Http\Resources\Admin\Admin;

use App\Http\Resources\Base\BaseResource;
use Carbon\Carbon;

class AdminResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'phone' => $this->phone,
            'status' => $this->status,
            'avatar' => $this->avatar_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
