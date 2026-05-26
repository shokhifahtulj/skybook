<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RouteUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin_airport_id' => ['required', 'exists:airports,id', 'different:destination_airport_id'],
            'destination_airport_id' => ['required', 'exists:airports,id', 'different:origin_airport_id'],
            'distance' => ['required', 'integer', 'min:1'],
            'estimated_duration' => ['required', 'integer', 'min:1'],
        ];
    }
}
