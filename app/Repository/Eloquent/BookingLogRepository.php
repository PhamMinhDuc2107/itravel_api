<?php

namespace App\Repository\Eloquent;

use App\Model\BookingLogModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\BookingLogRepositoryInterface;

class BookingLogRepository extends BaseRepository implements BookingLogRepositoryInterface
{
    public function model(): string
    {
        return BookingLogModel::class;
    }
}

