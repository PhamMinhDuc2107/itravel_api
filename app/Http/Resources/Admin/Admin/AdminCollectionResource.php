<?php
namespace App\Http\Resources\Admin\Admin;

use App\Http\Resources\Base\BaseResourceCollection;

class AdminCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => AdminResource::collection($this->resource),
        ];
    }
}
