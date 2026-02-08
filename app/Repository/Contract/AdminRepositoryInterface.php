<?php

namespace App\Repository\Contract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;


interface AdminRepositoryInterface
{
    /**
     * Get admin by email
     */
    public function getAdminByEmail(string $email): ?Model;

    public function getListAdmin(array $params): LengthAwarePaginator;

}
