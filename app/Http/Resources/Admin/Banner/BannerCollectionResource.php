<?php

namespace App\Http\Resources\Admin\Banner;

use App\Http\Resources\Base\BaseResourceCollection;

class BannerCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => BannerResource::collection($this->resource),
        ];
    }
}

