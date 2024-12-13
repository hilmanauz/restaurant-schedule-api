<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::query()->limit(1)->first();
        Schedule::create([
            "restaurant_id" => $restaurant->id,
            'day_of_week' => 'Mon',
            'open_time' => '11:30:00',
            'close_time' => '21:00:00',
        ]);
    }
}
