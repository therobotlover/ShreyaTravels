<?php

namespace Database\Seeders;

use App\Models\Tour;
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
        if (Tour::query()->exists()) {
            return;
        }

        Tour::query()->insert([
            [
                'title' => 'Day tour at Debotakhum',
                'location' => 'Bandarban',
                'duration_days' => 1,
                'base_price_bdt' => 5500,
                'hero_image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_active' => true,
                'is_featured_ongoing' => true,
                'next_start_date' => now()->addDays(7)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '3 Days at CoxBazar',
                'location' => 'Coxs Bazar',
                'duration_days' => 3,
                'base_price_bdt' => 12000,
                'hero_image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e',
                'is_active' => true,
                'is_featured_ongoing' => true,
                'next_start_date' => now()->addDays(14)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '3 Days at Sajek',
                'location' => 'Rangamati',
                'duration_days' => 3,
                'base_price_bdt' => 9800,
                'hero_image_url' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470',
                'is_active' => true,
                'is_featured_ongoing' => true,
                'next_start_date' => now()->addDays(20)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '3 Days at Shylet',
                'location' => 'Sylhet',
                'duration_days' => 3,
                'base_price_bdt' => 10500,
                'hero_image_url' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429',
                'is_active' => true,
                'is_featured_ongoing' => true,
                'next_start_date' => now()->addDays(25)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '4 Days at Kuakata',
                'location' => 'Patuakhali',
                'duration_days' => 4,
                'base_price_bdt' => 13500,
                'hero_image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee',
                'is_active' => true,
                'is_featured_ongoing' => false,
                'next_start_date' => now()->addDays(30)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '2 Days at Srimangal',
                'location' => 'Moulvibazar',
                'duration_days' => 2,
                'base_price_bdt' => 7800,
                'hero_image_url' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429',
                'is_active' => true,
                'is_featured_ongoing' => false,
                'next_start_date' => now()->addDays(18)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
