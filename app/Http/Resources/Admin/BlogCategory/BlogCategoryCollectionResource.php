<?php

namespace App\Http\Resources\Admin\BlogCategory;

use App\Http\Resources\Base\BaseResourceCollection;

class BlogCategoryCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => BlogCategoryResource::collection($this->resource),
        ];
    }
}

