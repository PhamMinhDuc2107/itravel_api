<?php

namespace App\Repository\Eloquent;

use App\Model\SupportTeamModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\SupportTeamRepositoryInterface;

class SupportTeamRepository extends BaseRepository implements SupportTeamRepositoryInterface
{
    public function model(): string
    {
        return SupportTeamModel::class;
    }
}

