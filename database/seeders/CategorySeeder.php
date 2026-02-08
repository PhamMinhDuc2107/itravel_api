<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Du lịch trong nước',
            'Du lịch nước ngoài',
            'Tour miền Bắc',
            'Tour miền Trung',
            'Tour miền Nam',
            'Tour Tây Bắc',
            'Tour Tây Nguyên',
            'Tour biển đảo',
            'Tour khám phá',
            'Tour nghỉ dưỡng',
        ];

        $data = [];
        foreach ($categories as $index => $name) {
            $data[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'parent_id' => null,
                'description' => fake()->sentence(10),
                'position' => $index,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('categories')->insert($data);
    }
}

