<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post(
            "/api/users/register",
            [
                "username" => "johndoe",
                "password" => "rahasia",
                "name" => "Hilman Auzan Mulyono",
                "role" => "admin"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" =>
                    [
                        "username" => "johndoe",
                        "name" => "Hilman Auzan Mulyono",
                        "role" => "admin"
                    ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post(
            "/api/users/register",
            [
                "username" => "",
                "password" => "",
                "name" => "",
                "role" => ""
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
                    ],
                    "role" => [
                        "The role field is required."
                    ]
                ]
            ]);
    }

    public function testRegisterFailedRole()
    {
        $this->post(
            "/api/users/register",
            [
                "username" => "",
                "password" => "",
                "name" => "",
                "role" => "coba"
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
                    ],
                    "role" => [
                        "The selected role is invalid."
                    ]
                ]
            ]);
    }

    public function testRegisterAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post(
            "/api/users/register",
            [
                "username" => "johndoe",
                "password" => "rahasia",
                "name" => "Hilman Auzan Mulyono",
                "role" => "admin"
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
                        "name" => "test",
                        "role" => "admin"
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
            "Authorization" => "Bearer test"
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
            "Authorization" => "Bearer salah"
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
