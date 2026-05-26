<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAirportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'iata_code' => [
                'required', 
                'string', 
                'size:3', 
                Rule::unique('airports')->ignore($this->airport)
            ],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'max:255'],
        ];
    }
}