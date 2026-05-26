<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FlightStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'flight_number' => ['required', 'string', 'max:255', 'unique:flights,flight_number'],
            'airline_id' => ['required', 'exists:airlines,id'],
            'route_id' => ['required', 'exists:routes,id'],
            'aircraft_id' => ['nullable', 'exists:aircrafts,id'],
        ];
    }
}
