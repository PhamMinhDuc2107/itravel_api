<?php

namespace App\Context;

use Illuminate\Support\Facades\Context;

class AppContext
{
    public static function setRequestId(string $id): void
    {
        Context::add('request_id', $id);
    }

    public static function getRequestId(): ?string
    {
        return Context::get('request_id');
    }

    public static function setUserId(?int $id): void
    {
        if ($id) {
            Context::add('user_id', $id);
        }
    }

    public static function getUserId(): ?int
    {
        return Context::get('user_id');
    }
}
