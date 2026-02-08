<?php

namespace App\Repository\Eloquent;

use App\Model\BlogCategoryModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\BlogCategoryRepositoryInterface;

class BlogCategoryRepository extends BaseRepository implements BlogCategoryRepositoryInterface
{
    public function model(): string
    {
        return BlogCategoryModel::class;
    }
}

