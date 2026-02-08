<?php

namespace App\Repository\Base;

use App\Context\QueryContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @template T of Model
 */
interface RepositoryInterface
{
    /**
     * Get all records
     */
    public function getAll(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get new query builder instance
     */
    public function newQuery(): Builder;

    /**
     * Paginate records
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    /**
     * Find record by ID
     */
    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Find record by ID or fail
     */
    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model;

    /**
     * Find record by conditions
     */
    public function findBy(array $conditions, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Get all records matching conditions
     */
    public function findAllBy(array $conditions, array $columns = ['*'], array $relations = []): Collection;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update record by ID
     */
    public function update(int|string $id, array $data): Model|bool;

    /**
     * Update or create record
     */
    public function updateOrCreate(array $conditions, array $data): Model;

    /**
     * Delete record by ID
     */
    public function delete(int|string $id): bool;

    /**
     * Delete multiple records by IDs
     */
    public function deleteMultiple(array $ids): int;

    /**
     * Delete records by conditions
     */
    public function deleteBy(array $conditions): int;

    /**
     * Count records
     */
    public function count(array $conditions = []): int;

    /**
     * Check if record exists
     */
    public function exists(array $conditions): bool;

    /**
     * Get first record
     */
    public function first(array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Get records with relations
     */
    public function with(array $relations): self;

    /**
     * Order records
     */
    public function orderBy(string $column, string $direction = 'asc'): self;

    /**
     * Get model instance
     */
    public function getModel(): Model;

    /**
     * Reset model instance
     */
    public function resetModel(): void;

    public function applyFilters(Builder $query, QueryContext $context, array $searchFields = []): LengthAwarePaginator;
    public function applyOnlyPagi(Builder $query, QueryContext $context): LengthAwarePaginator;
    public function applySort(Builder $query, QueryContext $context): Builder;
    public function applySearch(Builder $query, QueryContext $context, array $searchFields = []): Builder;
    public function applyCriteria(Builder $query, QueryContext $context, array $searchFields = []): Builder;
    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator;
}
