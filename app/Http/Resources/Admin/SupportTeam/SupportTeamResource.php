<?php

namespace App\Http\Resources\Admin\SupportTeam;

use App\Http\Resources\Base\BaseResource;

class SupportTeamResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'zalo' => $this->zalo,
            'avatar' => $this->avatar_url,
            'role' => $this->role,
            'group' => $this->group,
            'position' => $this->position,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

