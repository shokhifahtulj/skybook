<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Flight;
use App\Models\FlightSchedule;

class ScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        $rules = [
            'flight_id' => 'required|exists:flights,id',
            'departure_datetime' => 'required|date',
            'arrival_datetime' => 'required|date|after:departure_datetime',
            'status' => 'required|in:scheduled,boarding,departed,arrived,cancelled,delayed',
            'terminal' => 'nullable|string|max:50',
            'gate' => 'nullable|string|max:50',
            
            // Dynamic Pricing Validation
            'prices' => 'required|array|min:1',
            'prices.*.cabin_class' => 'required|in:economy,premium_economy,business,first',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.quota' => 'required|integer|min:1',
        ];

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $flight = Flight::with('aircraft')->find($this->flight_id);
            if (!$flight) return;

            // 1. Check Aircraft Capacity
            if ($flight->aircraft) {
                $totalQuota = 0;
                if ($this->has('prices') && is_array($this->prices)) {
                    foreach ($this->prices as $priceData) {
                        $totalQuota += (int) ($priceData['quota'] ?? 0);
                    }
                }
                
                if ($totalQuota > $flight->aircraft->capacity) {
                    $validator->errors()->add('prices', 'Total kuota (' . $totalQuota . ') melebihi kapasitas pesawat (' . $flight->aircraft->capacity . ' kursi).');
                }
            }

            // 2. Check Aircraft Schedule Conflict
            if ($flight->aircraft_id) {
                $conflict = FlightSchedule::whereHas('flight', function($q) use ($flight) {
                    $q->where('aircraft_id', $flight->aircraft_id);
                })
                ->where(function($q) {
                    $q->whereBetween('departure_datetime', [$this->departure_datetime, $this->arrival_datetime])
                      ->orWhereBetween('arrival_datetime', [$this->departure_datetime, $this->arrival_datetime])
                      ->orWhere(function($sub) {
                          $sub->where('departure_datetime', '<=', $this->departure_datetime)
                              ->where('arrival_datetime', '>=', $this->arrival_datetime);
                      });
                });

                if ($this->route('schedule')) {
                    $conflict->where('id', '!=', $this->route('schedule')->id);
                }

                if ($conflict->exists()) {
                    $validator->errors()->add('departure_datetime', 'Pesawat yang digunakan (' . $flight->aircraft->model . ') memiliki jadwal penerbangan lain yang bentrok pada rentang waktu tersebut.');
                }
            }
        });
    }

    public function attributes()
    {
        return [
            'flight_id' => 'Penerbangan',
            'departure_datetime' => 'Waktu Keberangkatan',
            'arrival_datetime' => 'Waktu Kedatangan',
            'status' => 'Status',
            'prices.*.cabin_class' => 'Kelas Kabin',
            'prices.*.price' => 'Harga',
            'prices.*.quota' => 'Kuota',
        ];
    }
}
