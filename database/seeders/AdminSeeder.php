<?php

namespace Database\Seeders;

use App\Enum\ActiveStateEnum;
use App\Model\AdminModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 1 admin mặc định
        AdminModel::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '0909090909',
            'status' => ActiveStateEnum::Active->value,
        ]);
    }
}

