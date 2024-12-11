<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post(
            "/api/users",
            [
                "username" => "johndoe",
                "password" => "rahasia",
                "name" => "Hilman Auzan Mulyono"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" =>
                    [
                        "username" => "johndoe",
                        "name" => "Hilman Auzan Mulyono"
                    ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post(
            "/api/users",
            [
                "username" => "",
                "password" => "",
                "name" => ""
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testRegisterAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post(
            "/api/users",
            [
                "username" => "johndoe",
                "password" => "rahasia",
                "name" => "Hilman Auzan Mulyono"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" =>
                    [
                        "username" => ["username already registered"],
                    ]
            ]);
    }

    public function testLoginSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->post(
            "/api/users/login",
            [
                "username" => "test",
                "password" => "test",
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" =>
                    [
                        "username" => "test",
                        "name" => "test"
                    ]
            ]);

        $user = User::where("username", "test")->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailed(): void
    {
        $this->post(
            "/api/users/login",
            [
                "username" => "tests",
                "password" => "tests",
            ]
        )->assertStatus(401)
            ->assertJson([
                "errors" =>
                    [
                        "message" => ["username or password is invalid"],
                    ]
            ]);
    }

    public function testLoginFailedWrongPassword(): void
    {
        $this->post(
            "/api/users/login",
            [
                "username" => "test",
                "password" => "passSalah",
            ]
        )->assertStatus(401)
            ->assertJson([
                "errors" =>
                    [
                        "message" => ["username or password is invalid"],
                    ]
            ]);
    }

    public function testinglogoutSuccess(): void
    {
        $this->seed([UserSeeder::class]);
        $this->delete("/api/users/logout", [], [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson(
                [
                    "data" => true
                ]
            );

        $user = User::where("username", "test")->first();
        self::assertNull($user->token);
    }

    public function testinglogoutFailed(): void
    {
        $this->seed([UserSeeder::class]);
        $this->delete("/api/users/logout", [], [
            "Authorization" => "salah"
        ])->assertStatus(401)
            ->assertJson(
                [
                    "errors" => [
                        "message" => [
                            "unauthorized"
                        ]
                    ]
                ]
            );
    }
}
