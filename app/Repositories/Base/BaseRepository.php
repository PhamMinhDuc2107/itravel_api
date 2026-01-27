<?php

// app/Repositories/Base/BaseRepository.php
namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Container\Container;
use Exception;

/**
 * @template T of Model
 * @implements RepositoryInterface<T>
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Lấy tên Model tương ứng (Các class con phải định nghĩa hàm này)
     * @return string
     */
    abstract public function getModel();

    /**
     * Khởi tạo Model
     */
    public function setModel()
    {
        // Sử dụng Service Container để khởi tạo Model
        $model = app()->make($this->getModel());

        if (!$model instanceof Model) {
            throw new Exception("Class {$this->getModel()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model;
    }

    public function getAll(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->paginate($perPage, $columns);
    }

    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id, $columns);
    }

    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->model->with($relations)->findOrFail($id, $columns);
    }

    public function findBy(array $conditions, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->with($relations)->where($conditions)->first($columns);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int|string $id, array $data): Model|bool
    {
        $record = $this->find($id);

        if ($record) {
            $record->update($data);
            return $record;
        }

        return false;
    }

    public function delete(int|string $id): bool
    {
        $record = $this->find($id);

        if ($record) {
            return $record->delete();
        }

        return false;
    }
}
