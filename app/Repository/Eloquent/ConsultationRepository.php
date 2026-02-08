<?php

namespace App\Repository\Eloquent;

use App\Model\ConsultationModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\ConsultationRepositoryInterface;

class ConsultationRepository extends BaseRepository implements ConsultationRepositoryInterface
{
    public function model(): string
    {
        return ConsultationModel::class;
    }
}

