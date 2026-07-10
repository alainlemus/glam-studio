<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Stylist;
use App\Models\StylistSchedule;
use Illuminate\Database\Seeder;

class StylistScheduleSeeder extends Seeder
{
    public function run(): void
    {
        StylistSchedule::query()->delete();
        foreach (Stylist::all() as $stylist) {
            for ($day = 1; $day <= 6; $day++) {
                StylistSchedule::create([
                    'stylist_id' => $stylist->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '19:00:00',
                    'is_active' => true,
                ]);
            }
        }
    }
}