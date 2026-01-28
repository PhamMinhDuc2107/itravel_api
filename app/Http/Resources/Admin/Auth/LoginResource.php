<?php

namespace App\Http\Resources\Admin\Auth;

use App\Http\Resources\Base\BaseResource;
use Illuminate\Http\Request;

class LoginResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return $this->resource;
    }
}
