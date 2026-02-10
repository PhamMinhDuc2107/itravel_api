<?php

namespace App\Repository\Contract;

use App\Repository\Base\RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface HotelRepositoryInterface extends RepositoryInterface
{
    public function getListHotel(array $params): LengthAwarePaginator;
}


