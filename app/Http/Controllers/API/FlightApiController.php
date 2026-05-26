<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FlightRequest;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Route;
use Illuminate\Http\Request;

class FlightApiController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Flight::class);

        $search = trim((string) $request->input('search', ''));
        $perPage = max(1, (int) $request->input('per_page', 15));
        $sortField = $this->sanitizeSort($request->input('sort'), ['flight_number', 'created_at']);
        $direction = $this->sanitizeDirection($request->input('direction'));

        $query = Flight::query()
            ->with(['schedules', 'airline', 'route.origin', 'route.destination', 'aircraft']);

        if ($search !== '') {
            $query->where(function ($scope) use ($search) {
                $scope->where('flight_number', 'like', "%{$search}%")
                    ->orWhereHas('airline', fn ($airlineQuery) => $airlineQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('route.origin', fn ($airportQuery) => $airportQuery->where('iata_code', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%"))
                    ->orWhereHas('route.destination', fn ($airportQuery) => $airportQuery->where('iata_code', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%"));
            });
        }

        $paginator = $query->orderBy($sortField, $direction)
            ->paginate($perPage);

        return $this->paginatedResponse(
            'Data berhasil ditampilkan',
            $paginator,
            fn (Flight $flight) => $this->transformFlight($flight),
            [
                'filters' => ['search' => $search],
                'sort' => $sortField,
                'direction' => $direction,
            ]
        );
    }

    public function show(Flight $flight)
    {
        $this->authorize('view', $flight);

        $flight->load(['schedules', 'airline', 'route.origin', 'route.destination', 'aircraft']);

        return $this->successResponse('Data berhasil ditampilkan', $this->transformFlight($flight));
    }

    public function store(FlightRequest $request)
    {
        $this->authorize('create', Flight::class);

        $payload = $this->normalizePayload($request->validated(), $request);
        $flight = Flight::create($payload);
        $flight->load(['schedules', 'airline', 'route.origin', 'route.destination', 'aircraft']);

        return $this->successResponse('Penerbangan berhasil dibuat', $this->transformFlight($flight));
    }

    public function update(FlightRequest $request, Flight $flight)
    {
        $this->authorize('update', $flight);

        $payload = $this->normalizePayload($request->validated(), $request);
        $flight->update($payload);
        $flight->load(['schedules', 'airline', 'route.origin', 'route.destination', 'aircraft']);

        return $this->successResponse('Penerbangan berhasil diperbarui', $this->transformFlight($flight));
    }

    public function destroy(Flight $flight)
    {
        $this->authorize('delete', $flight);

        $flight->delete();

        return $this->successResponse('Penerbangan berhasil dihapus', null);
    }

    protected function normalizePayload(array $validated, FlightRequest $request): array
    {
        $payload = array_filter($validated, fn ($key) => ! in_array($key, ['kode_penerbangan', 'maskapai', 'asal', 'tujuan', 'harga'], true), ARRAY_FILTER_USE_KEY);

        $airlineName = (string) ($request->input('maskapai') ?: 'SkyBook Air');
        $airlineCode = strtoupper(substr($airlineName, 0, 10) ?: 'SKB');

        $airline = Airline::firstOrCreate([
            'code' => $airlineCode,
        ], [
            'name' => $airlineName,
        ]);

        $originCode = strtoupper(substr((string) $request->input('asal', 'CGK'), 0, 3));
        $destinationCode = strtoupper(substr((string) $request->input('tujuan', 'DPS'), 0, 3));

        $originAirport = Airport::firstOrCreate([
            'iata_code' => $originCode,
        ], [
            'name' => $originCode,
            'city' => $originCode,
            'country' => 'Indonesia',
            'timezone' => 'Asia/Jakarta',
        ]);

        $destinationAirport = Airport::firstOrCreate([
            'iata_code' => $destinationCode,
        ], [
            'name' => $destinationCode,
            'city' => $destinationCode,
            'country' => 'Indonesia',
            'timezone' => 'Asia/Makassar',
        ]);

        $route = Route::firstOrCreate([
            'origin_airport_id' => $originAirport->id,
            'destination_airport_id' => $destinationAirport->id,
        ], [
            'estimated_duration' => 120,
        ]);

        return array_merge($payload, [
            'flight_number' => strtoupper((string) ($validated['flight_number'] ?? $request->input('kode_penerbangan') ?? 'SKB-001')),
            'airline_id' => $validated['airline_id'] ?? $airline->id,
            'route_id' => $validated['route_id'] ?? $route->id,
            'aircraft_id' => $validated['aircraft_id'] ?? null,
        ]);
    }

    protected function transformFlight(Flight $flight): array
    {
        return [
            'id' => $flight->id,
            'flight_number' => $flight->flight_number,
            'kode_penerbangan' => $flight->flight_number,
            'airline' => $flight->airline,
            'maskapai' => $flight->airline?->name,
            'route' => [
                'id' => $flight->route?->id,
                'origin' => $flight->route?->origin,
                'destination' => $flight->route?->destination,
                'estimated_duration' => $flight->route?->estimated_duration,
            ],
            'asal' => $flight->route?->origin?->iata_code,
            'tujuan' => $flight->route?->destination?->iata_code,
            'aircraft' => $flight->aircraft,
            'schedules' => $flight->schedules,
            'harga' => $flight->route?->estimated_duration,
            'created_at' => $flight->created_at,
            'updated_at' => $flight->updated_at,
        ];
    }

    protected function paginatedResponse(string $message, $paginator, callable $transformer, array $meta = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->getCollection()->map($transformer)->values(),
            'meta' => array_merge([
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ], $meta),
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

    protected function successResponse(string $message, mixed $data, int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
