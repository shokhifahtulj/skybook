<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'flight_id' => 'required|exists:flights,id',
            'tanggal' => 'required|date',
            'jam_berangkat' => 'required|date_format:H:i',
            'jam_tiba' => 'required|date_format:H:i|after:jam_berangkat',
            'kapasitas' => 'required|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (! $this->hasAny(['flight_id', 'tanggal', 'jam_berangkat', 'jam_tiba'])) {
                return;
            }

            $flightId = $this->input('flight_id');
            $tanggal = $this->input('tanggal');
            $start = Carbon::parse("{$tanggal} {$this->input('jam_berangkat')}");
            $end = Carbon::parse("{$tanggal} {$this->input('jam_tiba')}");

            if ($start->gte($end)) {
                $validator->errors()->add('jam_tiba', 'Jam tiba harus lebih besar dari jam berangkat.');
                return;
            }

            $query = Schedule::where('flight_id', $flightId)
                ->where('tanggal', $tanggal)
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('jam_berangkat', [$start->format('H:i:s'), $end->format('H:i:s')])
                        ->orWhereBetween('jam_tiba', [$start->format('H:i:s'), $end->format('H:i:s')])
                        ->orWhere(function ($query) use ($start, $end) {
                            $query->where('jam_berangkat', '<=', $start->format('H:i:s'))
                                ->where('jam_tiba', '>=', $end->format('H:i:s'));
                        });
                });

            if ($this->route('schedule')) {
                $query->where('id', '!=', $this->route('schedule')->id);
            }

            if ($query->exists()) {
                $validator->errors()->add('jam_berangkat', 'Jadwal penerbangan untuk maskapai ini bentrok pada tanggal dan jam yang sama.');
            }
        });
    }
}
