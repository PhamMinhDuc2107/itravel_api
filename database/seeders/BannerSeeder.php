<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            'Banner chính trang chủ',
            'Banner tour đặc biệt',
            'Banner khuyến mãi',
            'Banner du lịch mùa hè',
            'Banner tour nước ngoài',
            'Banner nghỉ dưỡng',
            'Banner khám phá',
            'Banner biển đảo',
            'Banner Tây Bắc',
            'Banner miền Trung',
        ];

        $data = [];
        foreach ($banners as $index => $name) {
            $data[] = [
                'name' => $name,
                'image' => 'banner/' . ($index + 1) . '.jpg',
                'mobile_image' => fake()->boolean(70) ? 'banner/mobile/' . ($index + 1) . '.jpg' : null,
                'link' => fake()->url(),
                'target' => fake()->randomElement(['_self', '_blank']),
                'description' => fake()->sentence(10),
                'type' => 'home_slider',
                'position' => $index,
                'status' => 1,
                'start_at' => now(),
                'end_at' => now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('banners')->insert($data);
    }
}

