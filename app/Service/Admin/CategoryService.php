<?php

namespace App\Service\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->categoryRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $category = $this->categoryRepository->find($id);

        if (! $category) {
            throw new NotFoundException('Category', $id);
        }

        return $category;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->categoryRepository->create($data);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, $data): Model
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundException('Category', $id);
        }

        return DB::transaction(function () use ($id, $data) {
            return $this->categoryRepository->update($id, $data);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function destroy(int $id): bool
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundException('Category', $id);
        }

        return DB::transaction(function () use ($id) {
            return $this->categoryRepository->delete($id);
        });
    }
}

