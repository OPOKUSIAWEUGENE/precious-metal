<?php

namespace App\Http\Controllers\v0\Customers;

use App\Http\Controllers\Controller;
use App\Modules\v0\GoldWallets\Services\GoldWalletService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class GoldWalletController extends Controller
{
    public function __construct(protected GoldWalletService $service)
    {
    }

    #[OA\Get(
        path: "/v0/auth/customers/gold-wallets",
        summary: "Get the authenticated customer's gold wallets",
        tags: ["Customer API"],
        security: [["sanctum" => []]],
        description: "Returns the current authenticated customer wallets from the shared gold wallet store.",
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
}
