<?php

namespace App\Http\Controllers\v0\Rates;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Rate\StoreRateRequest;
use App\Modules\v0\Rates\Services\RateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Rate',
    title: 'Rate',
    description: 'A historical rate entry.',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'rate', type: 'string', example: '0.0500'),
        new OA\Property(property: 'rate_type', type: 'string', example: 'buying'),
        new OA\Property(property: 'status', type: 'string', example: 'active'),
        new OA\Property(property: 'effective_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'notes', type: 'string', nullable: true),
        new OA\Property(property: 'added_by', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class RateController extends Controller
{
    public function __construct(protected RateService $service)
    {
    }

    #[OA\Get(
        path: '/v0/rates',
        summary: 'Get all rates',
        tags: ['Rates'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Rate')),
                                new OA\Property(property: 'links', type: 'object'),
                                new OA\Property(property: 'meta', type: 'object'),
                            ]
                        ),
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        return $this->service->all((int) $request->query('per_page', 15));
    }

    #[OA\Post(
        path: '/v0/rates',
        summary: 'Set a new rate',
        tags: ['Rates'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['rate', 'rate_type'],
                properties: [
                    new OA\Property(property: 'rate', type: 'number', format: 'float', example: 0.0500),
                    new OA\Property(property: 'rate_type', type: 'string', enum: ['buying', 'selling']),
                    new OA\Property(property: 'effective_at', type: 'string', format: 'date-time', nullable: true),
                    new OA\Property(property: 'notes', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Rate'),
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function store(StoreRateRequest $request): JsonResponse
    {
        return $this->service->setRate($request->validated());
    }
}
