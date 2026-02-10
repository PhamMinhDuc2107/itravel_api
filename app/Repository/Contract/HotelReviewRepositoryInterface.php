<?php

namespace App\Repository\Contract;

use App\Repository\Base\RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface HotelReviewRepositoryInterface extends RepositoryInterface
{
    public function getListHotelReview(array $params): LengthAwarePaginator;
}


