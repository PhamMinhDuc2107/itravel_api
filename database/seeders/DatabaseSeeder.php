<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class, 
            CategorySeeder::class,
            LocationSeeder::class,
            HotelTypeSeeder::class,
            AmenitySeeder::class,
            BannerSeeder::class,
            BlogCategorySeeder::class,
            TourSeeder::class,
            HotelSeeder::class,
            BlogSeeder::class,
        ]);
    }
}
