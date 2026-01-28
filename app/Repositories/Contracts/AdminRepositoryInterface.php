<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;


interface AdminRepositoryInterface
{
    /**
     * Get admin by email
     */
    public function getAdminByEmail(string $email): ?Model;
}
