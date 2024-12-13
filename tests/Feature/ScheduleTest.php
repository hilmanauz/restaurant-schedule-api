<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\Schedule;
use Database\Seeders\Restaurant2Seeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\ScheduleSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);
        $restaurant = Restaurant::query()->limit(1)->first();
        $this->post(
            "/api/restaurants/" . $restaurant->id . "/schedules",
            [
                "day_of_week" => "mon",
                "open_time" => "11:30",
                "close_time" => "21:00",
            ],
            [
                "Authorization" => "Bearer test"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    "day_of_week" => "mon",
                    "open_time" => "11:30",
                    "close_time" => "21:00",
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);
        $restaurant = Restaurant::query()->limit(1)->first();
        $this->post(
            "/api/restaurants/" . $restaurant->id . "/schedules",
            [
                "day_of_week" => "",
                "open_time" => "",
                "close_time" => "",
            ],
            [
                "Authorization" => "Bearer test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "day_of_week" => [
                        "The day of week field is required."
                    ],
                    "open_time" => [
                        "The open time field is required."
                    ],
                    "close_time" => [
                        "The close time field is required."
                    ],
                ]
            ]);
    }

    public function testCreateOpenAndCloseTimeInvalidInput()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);
        $restaurant = Restaurant::query()->limit(1)->first();
        $this->post(
            "/api/restaurants/" . $restaurant->id . "/schedules",
            [
                "day_of_week" => "mon",
                "open_time" => "pagi",
                "close_time" => "siang",
            ],
            [
                "Authorization" => "Bearer test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "open_time" => ["The open time field must match the format H:i."],
                    "close_time" => [
                        "The close time field must match the format H:i.",
                        "The close time field must be a date after open time."
                    ]
                ]
            ]);
    }

    public function testCloseTimeIsBeforeOpenTimeFails()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);
        $restaurant = Restaurant::query()->limit(1)->first();
        $this->post(
            "/api/restaurants/" . $restaurant->id . "/schedules",
            [
                "day_of_week" => "mon",
                "open_time" => "16:00",
                "close_time" => "11:00",
            ],
            [
                "Authorization" => "Bearer test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "close_time" => [
                        "The close time field must be a date after open time."
                    ]
                ]
            ]);
    }

    public function testDuplicateScheduleForSameDayFails()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);
        $restaurant = Restaurant::query()->limit(1)->first();

        Schedule::create([
            "restaurant_id" => $restaurant->id,
            "day_of_week" => "mon",
            "open_time" => "10:00",
            "close_time" => "20:00",
        ]);

        $this->post(
            "/api/restaurants/" . $restaurant->id . "/schedules",
            [
                "day_of_week" => "mon",
                "open_time" => "12:30",
                "close_time" => "21:00",
            ],
            [
                "Authorization" => "Bearer test"
            ]
        )->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "A schedule for this day already exists for the restaurant."
                    ]
                ]
            ]);

    }

    public function testScheduleCreationWithSameDayOfWeekForDifferentRestaurants()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class, Restaurant2Seeder::class]);
        $restaurants = Restaurant::query()->limit(2);
        $restaurant1 = $restaurants->first();
        $restaurant2 = $restaurants->skip(1)->first();

        Schedule::create([
            "restaurant_id" => $restaurant1->id,
            "day_of_week" => "mon",
            "open_time" => "10:00",
            "close_time" => "20:00",
        ]);

        $this->post(
            "/api/restaurants/" . $restaurant2->id . "/schedules",
            [
                "day_of_week" => "mon",
                "open_time" => "12:30",
                "close_time" => "21:00",
            ],
            [
                "Authorization" => "Bearer test"
            ]
        )->assertStatus(status: 201)
            ->assertJson([
                "data" => [
                    "day_of_week" => "mon",
                    "open_time" => "12:30",
                    "close_time" => "21:00",
                ]
            ]);

    }

    public function testUpdateSchedule()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class, ScheduleSeeder::class]);

        $schedule = Schedule::query()->limit(1)->first();
        $this->put("/api/schedules/" . $schedule->id, [
            "day_of_week" => "tue",
            "open_time" => "12:30",
            "close_time" => "21:00",
        ], [
            "Authorization" => "Bearer test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "day_of_week" => "tue",
                    "open_time" => "12:30",
                    "close_time" => "21:00",
                ]
            ]);
    }

    public function testUpdateToSimilarDaySchedule()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class, ScheduleSeeder::class]);

        $schedule = Schedule::query()->limit(1)->first();
        $this->put("/api/schedules/" . $schedule->id, [
            "day_of_week" => "mon",
            "open_time" => "13:30",
            "close_time" => "21:00",
        ], [
            "Authorization" => "Bearer test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "day_of_week" => "mon",
                    "open_time" => "13:30",
                    "close_time" => "21:00",
                ]
            ]);
    }


    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class, ScheduleSeeder::class]);

        $schedule = Schedule::query()->limit(1)->first();

        $this->delete('/api/schedules/' . $schedule->id, [], [
            'Authorization' => 'Bearer test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

}
