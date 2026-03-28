<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use App\Modules\v0\Trips\Services\PassangerTripService;
use App\Modules\v0\Routes\Enums\RouteType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PassengerTripController extends Controller
{
    protected PassangerTripService $passengerTripService;

    public function __construct(PassangerTripService $passengerTripService)
    {
        $this->passengerTripService = $passengerTripService;
    }

    /**
     * List featured trips (for homepage).
     */
    public function featured(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $routeType = $request->input('route_type');
        $limit = (int) $request->input('limit', 10);

        $routeTypeEnum = $routeType ? RouteType::tryFrom($routeType) : null;

        return $this->passengerTripService->listFeaturedTrips($search, $routeTypeEnum, $limit);
    }

    /**
     * Search trips with pagination.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $routeType = $request->input('route_type');
        $page = (int) $request->input('page', 0);
        $size = (int) $request->input('size', 20);

        $routeTypeEnum = $routeType ? RouteType::tryFrom($routeType) : null;

        return $this->passengerTripService->searchTrips($search, $routeTypeEnum, $page, $size);
    }
}