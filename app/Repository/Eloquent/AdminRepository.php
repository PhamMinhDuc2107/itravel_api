<?php

namespace App\Repository\Eloquent;

use App\Model\AdminModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\AdminRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    public function model(): string
    {
        return AdminModel::class;
    }

    public function getAdminByEmail(string $email): ?Model
    {
        return $this->findBy(['email' => $email]);
    }
}
