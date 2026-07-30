<?php

namespace App\Http\Controllers\v0\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\v0\Asset\StoreWithdrawalRequest;
use App\Modules\v0\Assets\Services\AssetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AssetController extends Controller
{
    public function __construct(protected AssetService $service)
    {
    }

    #[OA\Get(
        path: '/v0/assets/wallets',
        summary: 'Get the authenticated customer asset wallets',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/AssetWallet')),
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                    ]
                )
            ),
        ]
    )]
    public function wallets(): JsonResponse
    {
        return $this->service->myWallets();
    }

    #[OA\Get(
        path: '/v0/assets/rate',
        summary: 'Get the current asset rates',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'rate_type',
                description: 'Filter by rate type',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['buying', 'selling']),
                example: 'buying'
            ),
        ],
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
                                new OA\Property(
                                    property: 'rates',
                                    type: 'object',
                                    additionalProperties: new OA\AdditionalProperties(ref: '#/components/schemas/CustomerRate')
                                ),
                                new OA\Property(property: 'status', type: 'string', example: 'success'),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function rate(Request $request): JsonResponse
    {
        return $this->service->currentRate($request->query('rate_type'));
    }

    #[OA\Post(
        path: '/v0/assets/withdrawals',
        summary: 'Request an asset withdrawal',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['asset_wallet_id', 'amount'],
                properties: [
                    new OA\Property(property: 'asset_wallet_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'amount', type: 'number', format: 'float', example: 2.0),
                    new OA\Property(property: 'conversion_type', type: 'string', example: 'gold_to_cash'),
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
                        new OA\Property(property: 'data', ref: '#/components/schemas/Withdrawal'),
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreWithdrawalRequest $request): JsonResponse
    {
        return $this->service->requestWithdrawal($request->validated());
    }
}
