<?php

namespace App\Repository\Contract;

use App\Repository\Base\RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface TourRepositoryInterface extends RepositoryInterface
{
    public function getListTour(array $params): LengthAwarePaginator;
}


