<?php

namespace App\Http\Requests;

use App\Models\FlightSchedule;
use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'schedule_id' => ['required', function ($attribute, $value, $fail) {
                if (! FlightSchedule::find($value) && ! Schedule::find($value)) {
                    $fail('The selected schedule id is invalid.');
                }
            }],
            'jumlah_tiket' => ['required', 'integer', 'min:1'],
            'wizard' => ['sometimes', 'boolean'],
            'passengers' => ['sometimes', 'array'],
            'passengers.*.first_name' => ['sometimes', 'required', 'string', 'min:2'],
            'passengers.*.last_name' => ['sometimes', 'required', 'string', 'min:2'],
            'passengers.*.identity_type' => ['sometimes', 'nullable', 'string'],
            'passengers.*.identity_number' => ['sometimes', 'nullable', 'string'],
            'passengers.*.date_of_birth' => ['sometimes', 'nullable', 'date'],
            'passengers.*.seat_number' => ['sometimes', 'required', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (! $this->filled('schedule_id') || ! $this->filled('jumlah_tiket')) {
                return;
            }

            $schedule = $this->resolveSchedule();

            if (! $schedule) {
                return;
            }

            $availableSeats = $schedule instanceof FlightSchedule
                ? (int) $schedule->available_seats
                : (int) $schedule->kapasitas;

            if ((int) $this->input('jumlah_tiket') > $availableSeats) {
                $validator->errors()->add('jumlah_tiket', 'Jumlah tiket melebihi kapasitas yang tersedia.');
            }

            if ($this->boolean('wizard')) {
                $passengers = $this->input('passengers', []);

                if (! is_array($passengers) || count($passengers) !== (int) $this->input('jumlah_tiket')) {
                    $validator->errors()->add('passengers', 'Jumlah data penumpang harus sesuai dengan jumlah tiket.');
                }
            }
        });
    }

    protected function resolveSchedule(): FlightSchedule|Schedule|null
    {
        $scheduleId = $this->input('schedule_id');

        if (! $scheduleId) {
            return null;
        }

        return FlightSchedule::find($scheduleId) ?? Schedule::find($scheduleId);
    }
}
