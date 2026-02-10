<?php

namespace App\Repository\Eloquent;

use App\Model\BookingModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\BookingRepositoryInterface;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function model(): string
    {
        return BookingModel::class;
    }
}

