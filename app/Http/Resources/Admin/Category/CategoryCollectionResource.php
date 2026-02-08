<?php

namespace App\Http\Resources\Admin\Category;

use App\Http\Resources\Base\BaseResourceCollection;

class CategoryCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => CategoryResource::collection($this->resource),
        ];
    }
}

