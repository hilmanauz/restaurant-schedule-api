<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "restaurant_id" => $this->restaurant_id,
            "day_of_week" => $this->day_of_week,
            "open_time" => $this->open_time,
            "close_time" => $this->close_time,
        ];
    }
}
