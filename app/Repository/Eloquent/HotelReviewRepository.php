<?php

namespace App\Repository\Eloquent;

use App\Model\HotelReviewModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\HotelReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class HotelReviewRepository extends BaseRepository implements HotelReviewRepositoryInterface
{
    public function model(): string
    {
        return HotelReviewModel::class;
    }

    public function getListHotelReview(array $params): LengthAwarePaginator
    {
        /** @var Builder $query */
        $query = $this->newQuery();

        return $this->applyFilters($query, $params);
    }
}


