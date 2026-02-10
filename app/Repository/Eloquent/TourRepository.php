<?php

namespace App\Repository\Eloquent;

use App\Model\TourModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\TourRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class TourRepository extends BaseRepository implements TourRepositoryInterface
{
    public function model(): string
    {
        return TourModel::class;
    }

    public function getListTour(array $params): LengthAwarePaginator
    {
        /** @var Builder $query */
        $query = $this->newQuery();

        return $this->applyFilters($query, $params);
    }
}


