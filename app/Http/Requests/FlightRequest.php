<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function prepareForValidation(): void
    {
        if ($this->filled('kode_penerbangan') && ! $this->filled('flight_number')) {
            $this->merge([
                'flight_number' => $this->input('kode_penerbangan'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'flight_number' => ['nullable', 'string', 'max:20', Rule::unique('flights', 'flight_number')->ignore($this->route('flight'))],
            'kode_penerbangan' => ['nullable', 'string', 'max:20'],
            'airline_id' => ['nullable', 'exists:airlines,id'],
            'route_id' => ['nullable', 'exists:routes,id'],
            'aircraft_id' => ['nullable', 'exists:aircrafts,id'],
            'maskapai' => ['nullable', 'string', 'max:100'],
            'asal' => ['nullable', 'string', 'max:100'],
            'tujuan' => ['nullable', 'string', 'max:100'],
            'harga' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
