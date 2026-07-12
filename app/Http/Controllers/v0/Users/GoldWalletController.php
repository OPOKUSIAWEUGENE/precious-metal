<?php

namespace App\Http\Controllers\v0\Users;

use App\Http\Controllers\Controller;
use App\Modules\v0\GoldWallets\Services\GoldWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GoldWallet',
    title: 'Gold Wallet',
    description: 'A gold wallet belonging to a user.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'wallet_reference', type: 'string', format: 'uuid'),
        new OA\Property(property: 'available_weight', type: 'string', example: '0.00000000'),
        new OA\Property(property: 'reserved_weight', type: 'string', example: '0.00000000'),
        new OA\Property(property: 'weight_unit', type: 'string', example: 'gram'),
        new OA\Property(property: 'purity', type: 'string', example: '0.999900'),
        new OA\Property(property: 'status', type: 'string', example: 'active'),
        new OA\Property(property: 'last_transaction_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class GoldWalletController extends Controller
{
    public function __construct(protected GoldWalletService $service)
    {
    }

    #[OA\Get(
        path: "/v0/gold-wallets",
        summary: "Get the authenticated user's gold wallets",
        tags: ["Gold Wallets"],
        security: [["sanctum" => []]],
        description: "Returns the current authenticated platform manager's gold wallets.",
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/GoldWallet")),
                        new OA\Property(property: "status", type: "string", example: "success"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function index(): JsonResponse
    {
        return $this->service->myWallets();
    }

    #[OA\Get(
        path: "/v0/gold-wallets/all",
        summary: "Get all users' gold wallets",
        tags: ["Gold Wallets"],
        security: [["sanctum" => []]],
        description: "Returns every gold wallet in the system for platform administrators.",
        parameters: [
            new OA\Parameter(name: "per_page", in: "query", schema: new OA\Schema(type: "integer", default: 15))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/GoldWallet")),
                                new OA\Property(property: "links", type: "object"),
                                new OA\Property(property: "meta", type: "object"),
                            ]
                        ),
                        new OA\Property(property: "status", type: "string", example: "success"),
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Forbidden")
        ]
    )]
    public function all(Request $request): JsonResponse
    {
        return $this->service->allWallets((int) $request->query('per_page', 15));
    }
}
