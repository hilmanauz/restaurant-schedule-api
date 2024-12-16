<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantCreateRequest;
use App\Http\Requests\RestaurantScheduleFilterGetRequest;
use App\Http\Requests\RestaurantUpdateRequest;
use App\Http\Resources\RestaurantCollection;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RestaurantController extends Controller
{
    public function create(RestaurantCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $restaurant = new Restaurant($data);
        $restaurant->save();

        return (new RestaurantResource($restaurant))
            ->response()
            ->setStatusCode(201);
    }

    public function update(int $id, RestaurantUpdateRequest $request): RestaurantResource
    {
        $restaurant = Restaurant::where("id", $id)->first();
        if (!$restaurant) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $data = $request->validated();
        $restaurant->fill($data);
        $restaurant->save();

        return new RestaurantResource($restaurant);
    }

    public function delete(int $id): JsonResponse
    {
        $restaurant = Restaurant::where('id', $id)->first();
        if (!$restaurant) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $restaurant->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function filterRestaurantSchedule(RestaurantScheduleFilterGetRequest $request): RestaurantCollection
    {
        $request->validated();
        $page = $request->input('page', 1);
        $restaurant = Restaurant::query()->with('schedules');

        $time = $request->input('time') ? Carbon::parse($request->input('time'))->format('H:i:s') : null;
        $dayOfWeek = $request->input('date') ? Carbon::parse($request->input('date'))->format('D') : null;

        $restaurant->whereHas('schedules', function ($restaurant) use ($time, $dayOfWeek) {
            if ($time && $dayOfWeek) {
                $restaurant->where(function ($subQuery) use ($time, $dayOfWeek) {
                    $subQuery->where('day_of_week', $dayOfWeek)
                        ->whereRaw('? BETWEEN open_time AND close_time', [$time]);
                })
                    ->orWhere(function ($subQuery) use ($time, $dayOfWeek) {
                        $previousDay = Carbon::parse($dayOfWeek)->subDay()->format('D');
                        $subQuery->where('day_of_week', $previousDay)
                            ->whereRaw('? BETWEEN open_time AND ADDTIME(close_time, "24:00:00")', [$time])
                            ->whereRaw('close_time < open_time');
                    });
            } else if ($time) {
                $restaurant->where(function ($restaurant) use ($time) {
                    $restaurant->where(function ($restaurant) use ($time) {
                        $restaurant->where('open_time', '<=', $time)
                            ->where('close_time', '>=', $time);
                    })
                        ->orWhere(function ($restaurant) use ($time) {
                            $restaurant->where('open_time', '<=', $time)
                                ->whereRaw("TIME(DATE_ADD(close_time, INTERVAL 1 DAY)) >= ?", [$time])
                                ->whereRaw('close_time < open_time');
                        });
                });
            } else if ($dayOfWeek) {
                $restaurant->where(function ($restaurant) use ($dayOfWeek) {
                    $restaurant->where('day_of_week', $dayOfWeek)
                        ->orWhere(function ($restaurant) use ($dayOfWeek) {
                            $prevDay = Carbon::createFromFormat('D', $dayOfWeek)->subDay()->format('D');
                            $restaurant->where('day_of_week', $prevDay)
                                ->whereRaw("TIME(DATE_ADD(close_time, INTERVAL 1 DAY)) > '00:00:00'");
                        });
                });
            }


        });
        $restaurant = $restaurant->paginate(perPage: 10, page: $page);

        return new RestaurantCollection($restaurant);
    }
}
