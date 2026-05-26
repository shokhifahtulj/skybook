<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FlightUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $flight = $this->route('flight');

        return [
            'flight_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('flights', 'flight_number')->ignore($flight?->id),
            ],
            'airline_id' => ['required', 'exists:airlines,id'],
            'route_id' => ['required', 'exists:routes,id'],
            'aircraft_id' => ['nullable', 'exists:aircrafts,id'],
        ];
    }
}
