<?php

namespace App\Repository\Eloquent;

use App\Model\HotelModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\HotelRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class HotelRepository extends BaseRepository implements HotelRepositoryInterface
{
    public function model(): string
    {
        return HotelModel::class;
    }

    public function getListHotel(array $params): LengthAwarePaginator
    {
        /** @var Builder $query */
        $query = $this->newQuery();

        return $this->applyFilters($query, $params);
    }
}


