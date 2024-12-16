<?php

use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("/users/register", [UserController::class, "register"]);
Route::post("/users/login", [UserController::class, "login"]);

Route::middleware("role.token:admin")->group(function () {
    Route::put("/schedules/{id}", [ScheduleController::class, "update"])->where("id", "[0-9]+");
    Route::delete("/schedules/{id}", [ScheduleController::class, "delete"])->where("id", "[0-9]+");
    Route::post("/restaurants/{idRestaurant}/schedules", [ScheduleController::class, "create"])->where("idRestaurant", "[0-9]+");
    Route::post("/restaurants", [RestaurantController::class, "create"]);
    Route::put("/restaurants/{id}", [RestaurantController::class, "update"])->where("id", "[0-9]+");
    Route::delete("/restaurants/{id}", [RestaurantController::class, "delete"])->where("id", "[0-9]+");
});

Route::middleware(["role.token:admin,user"])->group(function () {
    Route::delete("/users/logout", [UserController::class, "logout"]);
    Route::get("/restaurants", [RestaurantController::class, "filterRestaurantSchedule"]);
});
