<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            CitySeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            ExpenseCategorySeeder::class,
            ClientSeeder::class,
            StylistScheduleSeeder::class,
            AppointmentSeeder::class,
            MarketingCampaignSeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}