<?php

namespace Database\Seeders;

use App\Enum\ActiveStateEnum;
use App\Enum\AmenityTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'WiFi miễn phí', 'type' => AmenityTypeEnum::General->value, 'code' => 'wifi'],
            ['name' => 'Bể bơi', 'type' => AmenityTypeEnum::General->value, 'code' => 'pool'],
            ['name' => 'Phòng gym', 'type' => AmenityTypeEnum::General->value, 'code' => 'gym'],
            ['name' => 'Nhà hàng', 'type' => AmenityTypeEnum::General->value, 'code' => 'restaurant'],
            ['name' => 'Bar', 'type' => AmenityTypeEnum::General->value, 'code' => 'bar'],
            ['name' => 'Spa', 'type' => AmenityTypeEnum::General->value, 'code' => 'spa'],
            ['name' => 'Bãi đỗ xe', 'type' => AmenityTypeEnum::General->value, 'code' => 'parking'],
            ['name' => 'Lễ tân 24/7', 'type' => AmenityTypeEnum::General->value, 'code' => 'reception'],
            ['name' => 'Điều hòa', 'type' => AmenityTypeEnum::Room->value, 'code' => 'ac'],
            ['name' => 'TV', 'type' => AmenityTypeEnum::Room->value, 'code' => 'tv'],
        ];

        $data = [];
        foreach ($amenities as $index => $amenity) {
            $data[] = [
                'name' => $amenity['name'],
                'code' => $amenity['code'],
                'icon' => null,
                'type' => $amenity['type'],
                'position' => $index,
                'status' => ActiveStateEnum::Active->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('amenities')->insert($data);
    }
}

