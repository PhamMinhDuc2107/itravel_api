<?php

namespace Database\Seeders;

use App\Enum\ActiveStateEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotelTypeIds = DB::table('hotel_types')->pluck('id')->toArray();
        $locationIds = DB::table('locations')->pluck('location_id')->toArray();

        if (empty($hotelTypeIds) || empty($locationIds)) {
            $this->command->warn('Hotel Types or Locations not found. Please run HotelTypeSeeder and LocationSeeder first.');
            return;
        }

        $hotels = [
            'Khách sạn Grand Hà Nội',
            'Resort Phú Quốc Paradise',
            'Homestay Sapa View',
            'Villa Đà Lạt',
            'Khách sạn Nha Trang Beach',
            'Resort Hạ Long Bay',
            'Boutique Hotel Hội An',
            'Eco Resort Cát Bà',
            'Lodge Tây Bắc',
            'Resort Mũi Né',
        ];

        $data = [];
        foreach ($hotels as $index => $name) {
            $data[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'hotel_type_id' => fake()->randomElement($hotelTypeIds),
                'location_id' => fake()->randomElement($locationIds),
                'address' => fake()->address(),
                'latitude' => fake()->latitude(8, 23),
                'longitude' => fake()->longitude(102, 110),
                'star_rating' => fake()->numberBetween(2, 5),
                'price_from' => fake()->randomFloat(2, 500000, 5000000),
                'excerpt' => fake()->sentence(15),
                'content' => fake()->paragraphs(8, true),
                'policies' => fake()->paragraphs(3, true),
                'email' => fake()->email(),
                'phone' => fake()->phoneNumber(),
                'website' => fake()->url(),
                'check_in_time' => '14:00',
                'check_out_time' => '12:00',
                'is_featured' => fake()->randomElement([0, 1]),
                'view_count' => fake()->numberBetween(0, 2000),
                'status' => ActiveStateEnum::Active->value,
                'meta_title' => fake()->sentence(5),
                'meta_description' => fake()->sentence(10),
                'meta_keywords' => fake()->words(5, true),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('hotels')->insert($data);
    }
}

