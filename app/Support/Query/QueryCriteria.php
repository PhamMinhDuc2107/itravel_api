<?php

namespace App\Support\Query;

use App\Context\QueryContext;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class QueryCriteria
{
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

    public static function apply(Builder $query, QueryContext $context, array $searchableFields = []): Builder
    {
        self::applySearch($query, $context, $searchableFields);
        self::applySort($query, $context);

        return $query;
    }
}
