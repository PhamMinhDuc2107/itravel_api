<?php

namespace App\Repositories\Eloquent;

use App\Models\AdminModel;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Contracts\AdminRepositoryInterface;
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
