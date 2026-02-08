<?php

namespace App\Repository\Eloquent;

use App\Model\BannerModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\BannerRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BannerRepository extends BaseRepository implements BannerRepositoryInterface
{
    public function model(): string
    {
        return BannerModel::class;
    }

    public function getListBanner(array $params): LengthAwarePaginator
    {
        $query = $this->newQuery();
        return $this->applyFilters($query, $params);
    }
}

