<?php

namespace App\Support\Query;

use App\Context\QueryContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Paginator
{

    public static function paginate(Builder $query, QueryContext $context): LengthAwarePaginator
    {
        $limit = $context->limit;

        return $query->paginate($limit)->appends($context->params);
    }
}
