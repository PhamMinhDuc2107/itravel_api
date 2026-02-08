<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HotelTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Khách sạn',
            'Resort',
            'Homestay',
            'Villa',
            'Căn hộ',
            'Nhà nghỉ',
            'Lodge',
            'Hostel',
            'Boutique Hotel',
            'Eco Resort',
        ];

        $data = [];
        foreach ($types as $index => $name) {
            $data[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'image' => null,
                'position' => $index,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('hotel_types')->insert($data);
    }
}

