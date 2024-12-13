<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleCreateRequest;
use App\Http\Requests\ScheduleUpdateRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Restaurant;
use App\Models\Schedule;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function create(int $idRestaurant, ScheduleCreateRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $restaurant = Restaurant::where("id", $idRestaurant)->first();
            if (!$restaurant) {
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "data not found"
                        ]
                    ]
                ]));
            }
            $schedule = new Schedule($data);
            $schedule->restaurant_id = $restaurant->id;
            $schedule->save();

            return (new ScheduleResource($schedule))
                ->response()
                ->setStatusCode(201);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            'A schedule for this day already exists for the restaurant.'
                        ]
                    ]
                ])->setStatusCode(422));
            }

            throw $e;
        }

    }

    public function update(int $id, ScheduleUpdateRequest $request): ScheduleResource
    {
        try {
            $schedule = Schedule::where("id", $id)->first();
            if (!$schedule) {
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "data not found"
                        ]
                    ]
                ]));
            }
            $data = $request->validated();
            $schedule->fill($data);
            $schedule->save();

            return new ScheduleResource($schedule);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            'A schedule for this day already exists for the restaurant.'
                        ]
                    ]
                ])->setStatusCode(422));
            }

            throw $e;
        }

    }

    public function delete(int $id): JsonResponse
    {
        $schedule = Schedule::where('id', $id)->first();
        if (!$schedule) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $schedule->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
