<?php

namespace App\Http\Controllers\v0\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Fee\StoreFeeRequest;
use App\Modules\v0\Fees\Services\FeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Fee',
    title: 'Fee',
    description: 'A historical fee entry.',
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'fee', type: 'string', example: '0.0500'),
        new OA\Property(property: 'status', type: 'string', example: 'active'),
        new OA\Property(property: 'effective_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'notes', type: 'string', nullable: true),
        new OA\Property(property: 'added_by', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class FeeController extends Controller
{
    public function __construct(protected FeeService $service)
    {
    }

    #[OA\Get(
        path: '/v0/fees',
        summary: 'Get all fees',
        tags: ['Fees'],
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
                                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Fee')),
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
        path: '/v0/fees',
        summary: 'Set a new fee',
        tags: ['Fees'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fee'],
                properties: [
                    new OA\Property(property: 'fee', type: 'number', format: 'float', example: 0.0500),
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
                        new OA\Property(property: 'data', ref: '#/components/schemas/Fee'),
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function store(StoreFeeRequest $request): JsonResponse
    {
        return $this->service->setFee($request->validated());
    }
}
