<?php

namespace App\Repository\Eloquent;

use App\Model\AmenityModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\AmenityRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class AmenityRepository extends BaseRepository implements AmenityRepositoryInterface
{
    public function model(): string
    {
        return AmenityModel::class;
    }

    public function getListAmenity(array $params): LengthAwarePaginator
    {
        /** @var Builder $query */
        $query = $this->newQuery();

        return $this->applyFilters($query, $params);
    }
}


