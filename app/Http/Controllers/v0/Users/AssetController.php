<?php

namespace App\Http\Controllers\v0\Users;

use App\Http\Controllers\Controller;
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
        summary: 'Get all asset wallets',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function wallets(Request $request): JsonResponse
    {
        return $this->service->allWallets((int) $request->query('per_page', 15));
    }

    #[OA\Get(
        path: '/v0/assets/withdrawals',
        summary: 'Get all withdrawal requests',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 15))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object'),
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function withdrawals(Request $request): JsonResponse
    {
        return $this->service->allWithdrawals((int) $request->query('per_page', 15));
    }

    #[OA\Post(
        path: '/v0/assets/withdrawals/{id}/approve',
        summary: 'Approve a withdrawal request',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Withdrawal'),
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 422, description: 'Invalid state'),
        ]
    )]
    public function approve(string $id): JsonResponse
    {
        return $this->service->approveWithdrawal($id);
    }
}
