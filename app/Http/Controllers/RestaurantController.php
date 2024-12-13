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

        if ($request->input('date')) {
            $date = Carbon::parse($request->input('date'));
            $dayOfWeek = $date->format('D');
            Log::info(json_encode($dayOfWeek, JSON_PRETTY_PRINT));

            $restaurant->whereHas('schedules', function ($restaurant) use ($dayOfWeek) {
                $restaurant->where('day_of_week', $dayOfWeek);
            });
        }

        if ($request->input('time')) {
            $restaurant->whereHas('schedules', function ($restaurant) use ($request) {
                $restaurant->where('open_time', '<=', $request->input('time'))
                    ->where('close_time', '>=', $request->input('time'));
            });
        }
        $restaurant = $restaurant->paginate(perPage: 10, page: $page);

        return new RestaurantCollection($restaurant);
    }
}
