<?php

namespace App\Repository\Contract;

use App\Repository\Base\RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface AmenityRepositoryInterface extends RepositoryInterface
{
    public function getListAmenity(array $params): LengthAwarePaginator;
}


