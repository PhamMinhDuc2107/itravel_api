<?php

namespace App\Repository\Eloquent;

use App\Model\CategoryModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function model(): string
    {
        return CategoryModel::class;
    }

    public function getListCategory(array $params): LengthAwarePaginator
    {
        $query = $this->newQuery();
        return $this->applyFilters($query, $params);
    }
}

