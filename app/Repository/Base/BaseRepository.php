<?php

namespace App\Repository\Base;

use App\Support\Query\Paginator;
use App\Support\Query\QueryCriteria;
use App\Support\Query\QueryWrapper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Container\Container as App;
use App\Context\QueryContext;

/**
 * @template T of Model
 * @implements RepositoryInterface<T>
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var T
     */
    protected $model;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var string|null
     */
    protected $orderByColumn;

    /**
     * @var string
     */
    protected $orderByDirection = 'asc';

    /**
     * BaseRepository constructor
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract protected function model(): string;

    /**
     * Make Model instance
     *
     * @return T
     * @throws \Exception
     */
    protected function makeModel(): Model
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Get Model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Reset Model instance
     */
    public function resetModel(): void
    {
        $this->makeModel();
        $this->relations = [];
        $this->orderByColumn = null;
        $this->orderByDirection = 'asc';
    }

    /**
     * Get new query builder instance
     */
    public function newQuery(): Builder
    {
        $query = $this->model->newQuery();

        if (!empty($this->relations)) {
            $query->with($this->relations);
        }

        if ($this->orderByColumn) {
            $query->orderBy($this->orderByColumn, $this->orderByDirection);
        }

        return $query;
    }

    /**
     * Get all records
     */
    public function getAll(array $columns = ['*'], array $relations = []): Collection
    {
        $query = $this->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get($columns);
    }

    /**
     * Paginate records
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        $result = $query->paginate($perPage, $columns);
        $this->resetModel();

        return $result;
    }

    /**
     * Find record by ID
     */
    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model
    {
        $query = $this->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        $result = $query->find($id, $columns);
        $this->resetModel();

        return $result;
    }

    /**
     * Find record by ID or fail
     */
    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model
    {
        $query = $this->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        $result = $query->findOrFail($id, $columns);
        $this->resetModel();

        return $result;
    }

    /**
     * Find record by conditions
     */
    public function findBy(array $conditions, array $columns = ['*'], array $relations = []): ?Model
    {
        $query = $this->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $result = $query->first($columns);
        $this->resetModel();

        return $result;
    }

    /**
     * Get all records matching conditions
     */
    public function findAllBy(array $conditions, array $columns = ['*'], array $relations = []): Collection
    {
        $query = $this->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $result = $query->get($columns);
        $this->resetModel();

        return $result;
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        $result = $this->model->create($data);
        $this->resetModel();

        return $result;
    }

    /**
     * Update record by ID
     */
    public function update(int|string $id, array $data): Model|bool
    {
        $record = $this->find($id);

        if (!$record) {
            return false;
        }

        $record->update($data);
        $this->resetModel();

        return $record;
    }

    /**
     * Update or create record
     */
    public function updateOrCreate(array $conditions, array $data): Model
    {
        $result = $this->model->updateOrCreate($conditions, $data);
        $this->resetModel();

        return $result;
    }

    /**
     * Delete record by ID
     */
    public function delete(int|string $id): bool
    {
        $record = $this->find($id);

        if (!$record) {
            return false;
        }

        $result = $record->delete();
        $this->resetModel();

        return $result;
    }

    /**
     * Delete multiple records by IDs
     */
    public function deleteMultiple(array $ids): int
    {
        $result = $this->model->destroy($ids);
        $this->resetModel();

        return $result;
    }

    /**
     * Delete records by conditions
     */
    public function deleteBy(array $conditions): int
    {
        $query = $this->newQuery();

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $result = $query->delete();
        $this->resetModel();

        return $result;
    }

    /**
     * Count records
     */
    public function count(array $conditions = []): int
    {
        $query = $this->newQuery();

        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        $result = $query->count();
        $this->resetModel();

        return $result;
    }

    /**
     * Check if record exists
     */
    public function exists(array $conditions): bool
    {
        $query = $this->newQuery();

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $result = $query->exists();
        $this->resetModel();

        return $result;
    }

    /**
     * Get first record
     */
    public function first(array $columns = ['*'], array $relations = []): ?Model
    {
        $query = $this->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        $result = $query->first($columns);
        $this->resetModel();

        return $result;
    }

    /**
     * Load relations
     */
    public function with(array $relations): self
    {
        $this->relations = $relations;
        return $this;
    }

    /**
     * Order records
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orderByColumn = $column;
        $this->orderByDirection = $direction;
        return $this;
    }

    public function applyCriteria(Builder $query, QueryContext $context, array $searchFields = []): Builder
    {
        return QueryCriteria::apply($query, $context, $searchFields);
    }

    public function applySearch(Builder $query, QueryContext $context, array $searchFields = []): Builder
    {
        return QueryCriteria::applySearch($query, $context, $searchFields);
    }

    public function applySort(Builder $query, QueryContext $context): Builder
    {
        return QueryCriteria::applySort($query, $context);
    }


    public function applyOnlyPagi(Builder $query, QueryContext $context): LengthAwarePaginator
    {
        return Paginator::paginate($query, $context);
    }

    public function applyFilters(Builder $query, QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return QueryWrapper::handle($query, $context, $searchFields);
    }


    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        return $this->applyFilters($query, $context, $searchFields);
    }
}
