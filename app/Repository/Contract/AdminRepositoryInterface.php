<?php

namespace App\Repository\Contract;

use Illuminate\Database\Eloquent\Model;


interface AdminRepositoryInterface
{
    /**
     * Get admin by email
     */
    public function getAdminByEmail(string $email): ?Model;
}
