<?php

namespace App\Service\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\BlogCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class BlogCategoryService
{
    public function __construct(
        private BlogCategoryRepositoryInterface $blogCategoryRepository,
    ) {}

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->blogCategoryRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $category = $this->blogCategoryRepository->find($id);

        if (! $category) {
            throw new NotFoundException('Blog Category', $id);
        }

        return $category;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->blogCategoryRepository->create($data);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $category = $this->blogCategoryRepository->find($id);

        if (!$category) {
            throw new NotFoundException('Blog Category', $id);
        }

        return DB::transaction(function () use ($id, $data) {
            return $this->blogCategoryRepository->update($id, $data);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function destroy(int $id): bool
    {
        $category = $this->blogCategoryRepository->find($id);

        if (!$category) {
            throw new NotFoundException('Blog Category', $id);
        }

        return DB::transaction(function () use ($id) {
            return $this->blogCategoryRepository->delete($id);
        });
    }
}

