<?php

namespace App\Http\Resources\Admin\BankAccount;

use App\Http\Resources\Base\BaseResourceCollection;

class BankAccountCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => BankAccountResource::collection($this->resource),
        ];
    }
}

