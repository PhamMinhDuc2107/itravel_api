<?php

namespace Database\Seeders;

use App\Enum\BlogStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $blogCategoryIds = DB::table('blog_categories')->pluck('id')->toArray();
        $adminIds = DB::table('admins')->pluck('id')->toArray();

        if (empty($adminIds)) {
            $this->command->warn('Admins not found. Please create at least one admin first.');
            return;
        }

        $blogs = [
            '10 địa điểm du lịch đẹp nhất Việt Nam',
            'Kinh nghiệm du lịch Sapa mùa lúa chín',
            'Top 5 resort nghỉ dưỡng tại Phú Quốc',
            'Hướng dẫn du lịch Đà Lạt tự túc',
            'Ẩm thực Hà Nội - Những món ăn không thể bỏ qua',
            'Khám phá văn hóa Tây Bắc',
            'Review tour Hạ Long Bay 2 ngày 1 đêm',
            'Mẹo tiết kiệm khi du lịch nước ngoài',
            'Những bãi biển đẹp nhất miền Trung',
            'Lịch sử và văn hóa cố đô Huế',
        ];

        $data = [];
        foreach ($blogs as $index => $name) {
            $data[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'excerpt' => fake()->sentence(20),
                'content' => fake()->paragraphs(10, true),
                'image' => null,
                'category_id' => !empty($blogCategoryIds) ? fake()->randomElement($blogCategoryIds) : null,
                'author_id' => fake()->randomElement($adminIds),
                'status' => fake()->randomElement([BlogStatusEnum::Draft->value, BlogStatusEnum::Published->value, BlogStatusEnum::Pending->value]),
                'is_featured' => fake()->randomElement([0, 1]),
                'view_count' => fake()->numberBetween(0, 5000),
                'published_at' => fake()->boolean(70) ? now()->subDays(fake()->numberBetween(1, 30)) : null,
                'meta_title' => fake()->sentence(5),
                'meta_description' => fake()->sentence(10),
                'meta_keywords' => fake()->words(5, true),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('blogs')->insert($data);
    }
}

