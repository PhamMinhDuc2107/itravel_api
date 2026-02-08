<?php

namespace App\Repository\Eloquent;

use App\Model\LocationModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\LocationRepositoryInterface;

class LocationRepository extends BaseRepository implements LocationRepositoryInterface
{
    public function model(): string
    {
        return LocationModel::class;
    }
}

