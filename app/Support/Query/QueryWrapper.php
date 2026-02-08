<?php

namespace App\Support\Query;

use App\Context\QueryContext;
use Illuminate\Database\Eloquent\Builder;

class QueryWrapper
{
    public static function handle(Builder $query, QueryContext $context, array $searchableFields = [])
    {
        $query = QueryCriteria::apply($query, $context, $searchableFields);
        return Paginator::paginate($query, $context);
    }
}
