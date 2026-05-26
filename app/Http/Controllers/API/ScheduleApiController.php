<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleRequest;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleApiController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $perPage = max(1, (int) $request->input('per_page', 15));
        $sortField = $this->sanitizeSort($request->input('sort'), ['created_at', 'tanggal', 'jam_berangkat', 'kapasitas']);
        $direction = $this->sanitizeDirection($request->input('direction'));

        $query = Schedule::query()->with('flight');

        if ($search !== '') {
            $query->where(function ($scope) use ($search) {
                $scope->where('tanggal', 'like', "%{$search}%")
                    ->orWhere('jam_berangkat', 'like', "%{$search}%")
                    ->orWhere('jam_tiba', 'like', "%{$search}%")
                    ->orWhereHas('flight', fn ($flightQuery) => $flightQuery->where('flight_number', 'like', "%{$search}%"));
            });
        }

        $paginator = $query->orderBy($sortField, $direction)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditampilkan',
            'data' => $paginator->getCollection()->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'filters' => ['search' => $search],
                'sort' => $sortField,
                'direction' => $direction,
            ],
        ]);
    }

    public function store(ScheduleRequest $request)
    {
        $schedule = Schedule::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibuat',
            'data' => $schedule,
        ]);
    }

    public function update(ScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui',
            'data' => $schedule,
        ]);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus',
            'data' => null,
        ]);
    }

    protected function sanitizeSort(?string $sort, array $allowed): string
    {
        $sort = trim((string) $sort);

        return in_array($sort, $allowed, true) ? $sort : $allowed[0];
    }

    protected function sanitizeDirection(?string $direction): string
    {
        $direction = strtolower(trim((string) $direction));

        return in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';
    }
}
