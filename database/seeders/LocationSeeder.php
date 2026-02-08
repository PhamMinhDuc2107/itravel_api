<?php

namespace Database\Seeders;

use App\Enum\ActiveStateEnum;
use App\Enum\LocationTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Hà Nội', 'type' => LocationTypeEnum::Province->value],
            ['name' => 'Hồ Chí Minh', 'type' => LocationTypeEnum::Province->value],
            ['name' => 'Đà Nẵng', 'type' => LocationTypeEnum::Province->value],
            ['name' => 'Hạ Long', 'type' => LocationTypeEnum::Attraction->value],
            ['name' => 'Sapa', 'type' => LocationTypeEnum::Attraction->value],
            ['name' => 'Phú Quốc', 'type' => LocationTypeEnum::Attraction->value],
            ['name' => 'Nha Trang', 'type' => LocationTypeEnum::Province->value],
            ['name' => 'Đà Lạt', 'type' => LocationTypeEnum::Attraction->value],
            ['name' => 'Huế', 'type' => LocationTypeEnum::Province->value],
            ['name' => 'Hội An', 'type' => LocationTypeEnum::Attraction->value],
        ];

        $data = [];
        foreach ($locations as $index => $location) {
            $data[] = [
                'name' => $location['name'],
                'slug' => Str::slug($location['name']),
                'parent_location_id' => null,
                'description' => fake()->paragraph(3),
                'content' => fake()->paragraphs(5, true),
                'image' => null,
                'type' => $location['type'],
                'display_home' => fake()->randomElement([0, 1]),
                'is_feature' => fake()->randomElement([0, 1]),
                'is_departure' => fake()->randomElement([0, 1]),
                'is_destination' => fake()->randomElement([0, 1]),
                'position' => $index,
                'status' => ActiveStateEnum::Active->value,
                'meta_title' => fake()->sentence(5),
                'meta_description' => fake()->sentence(10),
                'meta_keywords' => fake()->words(5, true),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('locations')->insert($data);
    }
}

