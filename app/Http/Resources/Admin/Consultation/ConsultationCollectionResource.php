<?php

namespace App\Http\Resources\Admin\Consultation;

use App\Http\Resources\Base\BaseResourceCollection;

class ConsultationCollectionResource extends BaseResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => ConsultationResource::collection($this->resource),
        ];
    }
}

