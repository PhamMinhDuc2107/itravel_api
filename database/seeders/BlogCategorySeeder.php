<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Tin tức du lịch',
            'Kinh nghiệm du lịch',
            'Địa điểm nổi bật',
            'Ẩm thực',
            'Văn hóa',
            'Lịch sử',
            'Thiên nhiên',
            'Khách sạn',
            'Mẹo du lịch',
            'Review tour',
        ];

        $data = [];
        foreach ($categories as $index => $name) {
            $data[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => fake()->sentence(10),
                'position' => $index,
                'status' => 'active',
                'meta_title' => fake()->sentence(5),
                'meta_description' => fake()->sentence(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('blog_categories')->insert($data);
    }
}

