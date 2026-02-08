<?php

namespace App\Http\Resources\Admin\Blog;

use App\Http\Resources\Base\BaseResourceCollection;

class BlogCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => BlogResource::collection($this->resource),
        ];
    }
}

