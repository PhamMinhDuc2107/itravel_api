<?php

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @template T of Model
 */
interface RepositoryInterface
{

    public function getAll(array $columns = ['*'], array $relations = []): Collection;


    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model;

    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model;

    public function findBy(array $conditions, array $columns = ['*'], array $relations = []): ?Model;

    public function create(array $data): Model;

    public function update(int|string $id, array $data): Model|bool;

    public function delete(int|string $id): bool;
}
