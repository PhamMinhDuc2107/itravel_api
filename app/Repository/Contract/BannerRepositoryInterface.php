<?php

namespace App\Repository\Contract;

use App\Repository\Base\RepositoryInterface;

interface BannerRepositoryInterface extends RepositoryInterface
{
    public function getListBanner(array $params): \Illuminate\Pagination\LengthAwarePaginator;
}

