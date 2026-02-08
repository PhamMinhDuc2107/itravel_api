<?php

namespace App\Repository\Eloquent;

use App\Model\BlogModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\BlogRepositoryInterface;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public function model(): string
    {
        return BlogModel::class;
    }
}

