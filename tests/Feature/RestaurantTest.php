<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use Database\Seeders\DynamicDatabaseSeeder;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\ScheduleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post(
            "/api/restaurants",
            [
                "name" => "Kushi Tsuru"
            ],
            [
                "Authorization" => "Bearer test"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    "name" => "Kushi Tsuru"
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->post(
            "/api/restaurants",
            [
                "name" => ""
            ],
            [
                "Authorization" => "Bearer test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ],
                ]
            ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/restaurants',
            [
                "name" => "Kushi Tsuru"
            ],
            [
                "Authorization" => "Bearer salah"
            ]
        )->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testCreateWrongTokenInput()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/restaurants',
            [
                "name" => "Kushi Tsuru"
            ],
            [
                "Authorization" => "salah"
            ]
        )->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'token not provided or invalid'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);
        $restaurant = Restaurant::query()->limit(1)->first();

        $this->put('/api/restaurants/' . $restaurant->id, [
            "name" => "Kushi Kushi"
        ], [
            'Authorization' => 'Bearer test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    "name" => "Kushi Kushi"
                ]
            ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);

        $restaurant = Restaurant::query()->limit(1)->first();

        $this->put('/api/restaurants/' . $restaurant->id, [
            'name' => '',
        ], [
            'Authorization' => 'Bearer test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);

        $restaurant = Restaurant::query()->limit(1)->first();

        $this->delete('/api/restaurants/' . $restaurant->id, [], [
            'Authorization' => 'Bearer test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class]);

        $restaurant = Restaurant::query()->limit(1)->first();

        $this->delete('/api/restaurants/' . ($restaurant->id + 1), [], [
            'Authorization' => 'Bearer test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testDeleteAlsoDeleteRelatedSchedule()
    {
        $this->seed([UserSeeder::class, RestaurantSeeder::class, ScheduleSeeder::class]);
        $restaurant = Restaurant::query()->limit(1)->first();
        $this->delete('/api/restaurants/' . $restaurant->id, [], [
            'Authorization' => 'Bearer test'
        ])->assertStatus(200);
        $this->assertDatabaseMissing('schedules', ['restaurant_id' => $restaurant->id]);
    }

    public function testGetAllRestaurants()
    {
        $this->seed([UserSeeder::class, DynamicDatabaseSeeder::class]);

        $response = $this->get('/api/restaurants', [
            'Authorization' => 'Bearer test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(52, $response['meta']['total']);
    }

    public function testGetRestaurantsByDate()
    {
        $this->seed([UserSeeder::class, DynamicDatabaseSeeder::class]);
        $response = $this->get('/api/restaurants?date=2024-12-15', [
            'Authorization' => 'Bearer test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(50, $response['meta']['total']);
    }

    public function testGetRestaurantsByTime()
    {
        $this->seed([UserSeeder::class, DynamicDatabaseSeeder::class]);
        $response = $this->get('/api/restaurants?time=10:00', [
            'Authorization' => 'Bearer test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(15, $response['meta']['total']);
    }

    public function testGetRestaurantsByDateAndTime()
    {
        $this->seed([UserSeeder::class, DynamicDatabaseSeeder::class]);
        $response = $this->get('/api/restaurants?date=2024-12-12&time=12:00', [
            'Authorization' => 'Bearer test'
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(50, $response['meta']['total']);
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, DynamicDatabaseSeeder::class]);

        $response = $this->get('/api/restaurants?page=2', [
            'Authorization' => 'Bearer test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(52, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
