<?php

namespace App\Http\Resources\Admin\SupportTeam;

use App\Http\Resources\Base\BaseResourceCollection;

class SupportTeamCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => SupportTeamResource::collection($this->resource),
        ];
    }
}

