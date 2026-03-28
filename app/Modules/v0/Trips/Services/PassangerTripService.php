<?php

namespace App\Modules\v0\Trips\Services;

use App\Modules\BaseService;
use App\Modules\v0\Trips\Repositories\TripRepository;
use App\Modules\v0\Routes\Repositories\RouteStopRepository;
use App\Modules\v0\Routes\Enums\RouteType;
use App\Modules\v0\Trips\Models\Trip;
use App\Modules\v0\Routes\Models\RouteStop;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PassangerTripService extends BaseService
{
    use ApiResponse;

    protected TripRepository $tripRepository;
    protected RouteStopRepository $routeStopRepository;

    public function __construct(TripRepository $tripRepository, RouteStopRepository $routeStopRepository)
    {
        $this->tripRepository = $tripRepository;
        $this->routeStopRepository = $routeStopRepository;
    }

    /**
     * List featured trips (for homepage/landing page).
     */
    public function listFeaturedTrips(?string $search, ?RouteType $routeType, int $limit = 10): JsonResponse
    {
        try {
            $page = 0;
            $pageSize = max($limit, 1);
            
            $trips = $this->fetchTrips($search, $routeType, $page, $pageSize);
            $mappedTrips = $this->mapTrips($trips);

            return self::success($mappedTrips);
        } catch (\Exception $e) {
            Log::error('PassangerTripService::listFeaturedTrips - ' . $e->getMessage());
            return self::error($e->getMessage());
        }
    }

    /**
     * Search trips with pagination.
     */
    public function searchTrips(?string $search, ?RouteType $routeType, int $page = 0, int $size = 20): JsonResponse
    {
        try {
            $pageSize = max($size, 1);
            $pageNumber = max($page, 0);
            
            $tripPage = $this->fetchTrips($search, $routeType, $pageNumber, $pageSize);
            $mappedTrips = $this->mapTrips($tripPage);

            // Calculate pagination info
            // Since we're using a simple approach, we'll return the collection with meta
            return self::success([
                'data' => $mappedTrips,
                'meta' => [
                    'current_page' => $pageNumber + 1,
                    'per_page' => $pageSize,
                    'total' => $tripPage->count(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('PassangerTripService::searchTrips - ' . $e->getMessage());
            return self::error($e->getMessage());
        }
    }

    /**
     * Fetch trips based on search criteria.
     */
    protected function fetchTrips(?string $search, ?RouteType $routeType, int $page, int $size): Collection
    {
        $query = Trip::with(['route', 'vehicle', 'departureStop', 'arrivalStop']);

        if (!empty($search)) {
            $pattern = '%' . strtolower(trim($search)) . '%';
            $query->where(function ($q) use ($pattern) {
                $q->whereHas('route', function ($routeQuery) use ($pattern) {
                    $routeQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]);
                })
                ->orWhereHas('departureStop', function ($stopQuery) use ($pattern) {
                    $stopQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]);
                })
                ->orWhereHas('arrivalStop', function ($stopQuery) use ($pattern) {
                    $stopQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]);
                });
            });
        }

        if ($routeType !== null) {
            $query->whereHas('route', function ($routeQuery) use ($routeType) {
                $routeQuery->where('route_type', $routeType->value);
            });
        }

        // Order by scheduled time
        $trips = $query->orderBy('scheduled_at', 'asc')
            ->skip($page * $size)
            ->take($size)
            ->get();

        return $trips;
    }

    /**
     * Map trips to DTOs with stop names.
     */
    protected function mapTrips(Collection $trips): array
    {
        if ($trips->isEmpty()) {
            return [];
        }

        // Collect all stop IDs
        $stopIds = $trips->flatMap(function ($trip) {
            return array_filter([
                $trip->departure_stop_id,
                $trip->arrival_stop_id,
            ]);
        })->unique()->values();

        // Fetch stop names
        $stopNames = [];
        if ($stopIds->isNotEmpty()) {
            $stops = $this->routeStopRepository->findByIds($stopIds->toArray());
            foreach ($stops as $stop) {
                $stopNames[$stop->id] = $stop->name;
            }
        }

        // Map to DTOs
        return $trips->map(function ($trip) use ($stopNames) {
            return $this->buildDto($trip, $stopNames);
        })->toArray();
    }

    /**
     * Build trip DTO.
     */
    protected function buildDto(Trip $trip, array $stopNames): array
    {
        $route = $trip->route;
        $vehicle = $trip->vehicle;

        return [
            'id' => $trip->id,
            'route_id' => $route?->id,
            'route_name' => $route?->name,
            'route_type' => $route?->routeType?->value,
            'start_stop_name' => $route?->startStop?->name,
            'end_stop_name' => $route?->endStop?->name,
            'departure_stop_name' => $trip->departure_stop_id ? ($stopNames[$trip->departure_stop_id] ?? null) : null,
            'arrival_stop_name' => $trip->arrival_stop_id ? ($stopNames[$trip->arrival_stop_id] ?? null) : null,
            'trip_name' => $route?->name,
            'vehicle_identifier' => $vehicle?->fleet_identifier,
            'vehicle_plate_number' => $vehicle?->plate_number,
            'scheduled_at' => $trip->scheduled_at,
            'status' => $trip->status?->value,
        ];
    }
}