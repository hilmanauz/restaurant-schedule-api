<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ScheduleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'day_of_week' => [
                'required',
                Rule::in(['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']), // Enum validasi hari
                Rule::unique('schedules')->where(function ($query) {
                    return $query->where('restaurant_id', $this->restaurant_id);
                }),
            ],
            "open_time" => ["required", "date_format:H:i"],
            "close_time" => ["required", "date_format:H:i", "after:open_time"],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
