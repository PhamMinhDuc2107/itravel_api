<?php

namespace App\Repository\Contract;

use App\Repository\Base\RepositoryInterface;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function getListCategory(array $params): \Illuminate\Pagination\LengthAwarePaginator;
}

