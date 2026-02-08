<?php

namespace App\Http\Resources\Base;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseResourceCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                /**
                 * Format the instane as ISO8601
                 *
                 * @var string
                 * @example "2025-07-16T19:09:11+09:00"
                 */
                'timestamp' => now()->toIso8601String(),

                /**
                 * Application version
                 *
                 * @var string
                 * @example "1.0.0"
                 */
                'version' => config('app.version', '1.0.0')
            ]
        ];
    }
}
