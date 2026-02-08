<?php

namespace App\Repository\Eloquent;

use App\Model\AdminModel;
use App\Repository\Base\BaseRepository;
use App\Repository\Contract\AdminRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function getListAdmin(array $params): LengthAwarePaginator
    {
        $query = $this->newQuery();
        return $this->applyFilters($query, $params);
    }
}
