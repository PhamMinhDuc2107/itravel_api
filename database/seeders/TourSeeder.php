<?php

namespace Database\Seeders;

use App\Enum\TourStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = DB::table('categories')->pluck('category_id')->toArray();
        $locationIds = DB::table('locations')->pluck('location_id')->toArray();

        if (empty($categoryIds) || empty($locationIds)) {
            $this->command->warn('Categories or Locations not found. Please run CategorySeeder and LocationSeeder first.');
            return;
        }

        $tours = [
            'Tour Hà Nội - Sapa 3 ngày 2 đêm',
            'Tour Đà Nẵng - Hội An 4 ngày 3 đêm',
            'Tour Phú Quốc nghỉ dưỡng 5 ngày 4 đêm',
            'Tour Hạ Long Bay 2 ngày 1 đêm',
            'Tour Đà Lạt khám phá 3 ngày 2 đêm',
            'Tour Nha Trang - Đà Lạt 4 ngày 3 đêm',
            'Tour Huế - Hội An 3 ngày 2 đêm',
            'Tour Tây Bắc 5 ngày 4 đêm',
            'Tour miền Tây sông nước 3 ngày 2 đêm',
            'Tour Hà Nội - Hạ Long - Sapa 5 ngày 4 đêm',
        ];

        $data = [];
        foreach ($tours as $index => $name) {
            $code = 'TOUR-' . strtoupper(Str::random(8));
            $departureLocationId = fake()->randomElement($locationIds);
            $destinationLocationId = fake()->randomElement($locationIds);
            
            // Đảm bảo departure và destination khác nhau
            while ($departureLocationId === $destinationLocationId) {
                $destinationLocationId = fake()->randomElement($locationIds);
            }

            $data[] = [
                'code' => $code,
                'name' => $name,
                'slug' => Str::slug($name),
                'category_id' => fake()->randomElement($categoryIds),
                'departure_location_id' => $departureLocationId,
                'destination_location_id' => $destinationLocationId,
                'duration_days' => fake()->numberBetween(2, 7),
                'duration_nights' => fake()->numberBetween(1, 6),
                'is_recurring' => fake()->randomElement([0, 1]),
                'recurring_days' => null,
                'price_adult' => fake()->randomFloat(2, 1000000, 10000000),
                'price_child' => fake()->randomFloat(2, 500000, 5000000),
                'price_infant' => fake()->randomFloat(2, 0, 1000000),
                'excerpt' => fake()->sentence(15),
                'overview' => fake()->paragraphs(5, true),
                'policy' => fake()->paragraphs(3, true),
                'included' => fake()->sentence(20),
                'excluded' => fake()->sentence(20),
                'image' => null,
                'gallery' => null,
                'view_count' => fake()->numberBetween(0, 1000),
                'position' => $index,
                'status' => TourStatusEnum::Published->value,
                'meta_title' => fake()->sentence(5),
                'meta_description' => fake()->sentence(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('tours')->insert($data);
    }
}

