<?php

namespace App\Support\Query;

use App\Context\QueryContext;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class QueryCriteria
{
    /**
     * Apply search (LIKE) conditions
     */
    public static function applySearch(Builder $query, QueryContext $context, array $searchableFields = []): Builder
    {
        $model = $query->getModel();
        $keyword = $context->q;

        $searchable = !empty($searchableFields) ? $searchableFields : ($model->searchable ?? []);

        if (empty($keyword) || empty($searchable)) {
            return $query;
        }

        if ($context->searchBy) {
            if (!in_array($context->searchBy, $searchable)) {
                throw new BadRequestHttpException(__('message.search.invalid_column', [
                    'column' => $context->searchBy,
                    'allowed' => implode(', ', $searchable)
                ]));
            }

            return $query->where($context->searchBy, 'LIKE', "%{$keyword}%");
        }

        $query->where(function ($q) use ($keyword, $searchable) {
            foreach ($searchable as $field) {
                $q->orWhere($field, 'LIKE', "%{$keyword}%");
            }
        });

        return $query;
    }

    /**
     * Apply dynamic filters (exact match)
     *
     * Supports:
     * - Simple equality: ?status=active
     * - Multiple values (comma-separated): ?status=active,pending
     * - Multiple values (array): ?status[]=active&status[]=pending
     * - Range filters: ?price_from=100&price_to=500
     * - Null checks: ?field=null or ?field=!null
     */
    public static function applyFilters(Builder $query, QueryContext $context): Builder
    {
        if ($context->filters->isEmpty()) {
            return $query;
        }

        foreach ($context->filters->all() as $key => $value) {
            // Handle range filters (e.g., price_from, price_to, date_from, date_to)
            if (str_ends_with($key, '_from')) {
                $field = str_replace('_from', '', $key);
                $query->where($field, '>=', $value);
                continue;
            }

            if (str_ends_with($key, '_to')) {
                $field = str_replace('_to', '', $key);
                $query->where($field, '<=', $value);
                continue;
            }

            // Handle null checks
            if ($value === 'null') {
                $query->whereNull($key);
                continue;
            }

            if ($value === '!null') {
                $query->whereNotNull($key);
                continue;
            }

            // Handle multiple values (comma-separated or array)
            if (is_array($value)) {
                $query->whereIn($key, $value);
                continue;
            }

            if (is_string($value) && str_contains($value, ',')) {
                $values = array_map('trim', explode(',', $value));
                $query->whereIn($key, $values);
                continue;
            }

            // Simple equality
            $query->where($key, $value);
        }

        return $query;
    }

    /**
     * Apply sorting
     */
    public static function applySort(Builder $query, QueryContext $context): Builder
    {
        $model = $query->getModel();
        $sortable = $model->sortable ?? ['id'];

        $sortBy = $context->sort;
        $order = $context->order;

        if (in_array($sortBy, $sortable)) {
            $query->orderBy($sortBy, $order);
        } else {
            $query->orderBy($model->getKeyName(), 'asc');
        }

        return $query;
    }

    /**
     * Apply all criteria (search, filters, sort)
     */
    public static function apply(Builder $query, QueryContext $context, array $searchableFields = []): Builder
    {
        self::applySearch($query, $context, $searchableFields);
        self::applyFilters($query, $context);
        self::applySort($query, $context);

        return $query;
    }
}
