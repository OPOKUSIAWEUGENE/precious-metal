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
        path: '/v0/customers/assets/wallets',
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
        path: '/v0/customers/assets/rate',
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

    #[OA\Get(
        path: '/v0/customers/assets/balance',
        summary: 'Get the authenticated wallet balance',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function balance(): JsonResponse
    {
        return $this->service->myBalance();
    }

    #[OA\Get(
        path: '/v0/customers/assets/transactions',
        summary: 'Get the authenticated wallet transactions',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'operation', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'date_from', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'date_to', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function transactions(Request $request): JsonResponse
    {
        return $this->service->myTransactions((int) $request->query('per_page', 15), $request->only(['operation', 'status', 'date_from', 'date_to']));
    }

    #[OA\Get(
        path: '/v0/customers/assets/transactions/{reference}',
        summary: 'Get a wallet transaction by reference',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'reference', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not Found'),
        ]
    )]
    public function transaction(string $reference): JsonResponse
    {
        return $this->service->transactionByReference($reference);
    }

    #[OA\Get(
        path: '/v0/customers/assets/prices/current',
        summary: 'Get current gold pricing',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not Found'),
        ]
    )]
    public function currentPrice(): JsonResponse
    {
        return $this->service->currentPrice();
    }

    #[OA\Post(
        path: '/v0/customers/assets/quotes',
        summary: 'Create a wallet quote',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'operation', type: 'string', enum: ['buy', 'sell', 'transfer']),
            new OA\Property(property: 'cash_amount', type: 'number', format: 'float', nullable: true),
            new OA\Property(property: 'gold_weight', type: 'number', format: 'float', nullable: true),
            new OA\Property(property: 'recipient_id', type: 'string', format: 'uuid', nullable: true),
            new OA\Property(property: 'currency', type: 'string', nullable: true),
        ])),
        responses: [
            new OA\Response(response: 201, description: 'Created'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function quote(Request $request): JsonResponse
    {
        return $this->service->createQuote($request->validate([
            'operation' => 'required|string|in:buy,sell,transfer',
            'cash_amount' => 'nullable|numeric|min:0.01',
            'gold_weight' => 'nullable|numeric|min:0.00000001',
            'recipient_id' => 'nullable|uuid',
            'currency' => 'nullable|string|max:10',
        ]));
    }

    #[OA\Get(
        path: '/v0/customers/assets/quotes/{reference}',
        summary: 'Get a wallet quote by reference',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'reference', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 404, description: 'Not Found'),
        ]
    )]
    public function quoteShow(string $reference): JsonResponse
    {
        return $this->service->quoteByReference($reference);
    }

    #[OA\Post(
        path: '/v0/customers/assets/transfers/recipient-enquiry',
        summary: 'Resolve a transfer recipient',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'identifier_type', type: 'string', enum: ['email', 'phone', 'id']),
            new OA\Property(property: 'identifier', type: 'string'),
        ])),
        responses: [new OA\Response(response: 200, description: 'Success')]
    )]
    public function recipientEnquiry(Request $request): JsonResponse
    {
        return $this->service->recipientEnquiry($request->validate([
            'identifier_type' => 'required|string|in:email,phone,id',
            'identifier' => 'required|string',
        ]));
    }

    #[OA\Post(
        path: '/v0/customers/assets/transfers/quote',
        summary: 'Get a transfer quote',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'recipient_id', type: 'string', format: 'uuid'),
            new OA\Property(property: 'gold_weight', type: 'number', format: 'float'),
        ])),
        responses: [new OA\Response(response: 201, description: 'Created')]
    )]
    public function transferQuote(Request $request): JsonResponse
    {
        return $this->service->transferQuote($request->validate([
            'recipient_id' => 'required|uuid',
            'gold_weight' => 'required|numeric|min:0.00000001',
        ]));
    }

    #[OA\Post(
        path: '/v0/customers/assets/transfers',
        summary: 'Execute a gold transfer',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'recipient_id', type: 'string', format: 'uuid'),
            new OA\Property(property: 'gold_weight', type: 'number', format: 'float'),
            new OA\Property(property: 'quote_reference', type: 'string'),
            new OA\Property(property: 'narration', type: 'string', nullable: true),
        ])),
        responses: [new OA\Response(response: 201, description: 'Created')]
    )]
    public function transfer(Request $request): JsonResponse
    {
        return $this->service->executeTransfer($request->validate([
            'recipient_id' => 'required|uuid',
            'gold_weight' => 'required|numeric|min:0.00000001',
            'quote_reference' => 'required|string',
            'narration' => 'nullable|string|max:2000',
        ]));
    }

    #[OA\Post(
        path: '/v0/customers/assets/sales/quote',
        summary: 'Get a sell quotation',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'gold_weight', type: 'number', format: 'float'),
            new OA\Property(property: 'settlement_currency', type: 'string', nullable: true),
        ])),
        responses: [new OA\Response(response: 201, description: 'Created')]
    )]
    public function saleQuote(Request $request): JsonResponse
    {
        return $this->service->saleQuote($request->validate([
            'gold_weight' => 'required|numeric|min:0.00000001',
            'settlement_currency' => 'nullable|string|max:10',
        ]));
    }

    #[OA\Post(
        path: '/v0/customers/assets/sales',
        summary: 'Sell gold',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'quote_reference', type: 'string'),
            new OA\Property(property: 'credit_destination', type: 'string', example: 'cash_wallet'),
            new OA\Property(property: 'narration', type: 'string', nullable: true),
        ])),
        responses: [new OA\Response(response: 201, description: 'Created')]
    )]
    public function sale(Request $request): JsonResponse
    {
        return $this->service->executeSale($request->validate([
            'quote_reference' => 'required|string',
            'credit_destination' => 'nullable|string|max:50',
            'narration' => 'nullable|string|max:2000',
        ]));
    }

    #[OA\Post(
        path: '/v0/customers/assets/purchases/quote',
        summary: 'Get a purchase quote',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'cash_amount', type: 'number', format: 'float'),
            new OA\Property(property: 'currency', type: 'string', nullable: true),
        ])),
        responses: [new OA\Response(response: 201, description: 'Created')]
    )]
    public function purchaseQuote(Request $request): JsonResponse
    {
        return $this->service->purchaseQuote($request->validate([
            'cash_amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|max:10',
        ]));
    }

    #[OA\Post(
        path: '/v0/customers/assets/purchases',
        summary: 'Buy gold',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'quote_reference', type: 'string'),
            new OA\Property(property: 'payment_method', type: 'string', example: 'cash_wallet'),
            new OA\Property(property: 'narration', type: 'string', nullable: true),
        ])),
        responses: [new OA\Response(response: 201, description: 'Created')]
    )]
    public function purchase(Request $request): JsonResponse
    {
        return $this->service->executePurchase($request->validate([
            'quote_reference' => 'required|string',
            'payment_method' => 'nullable|string|max:50',
            'narration' => 'nullable|string|max:2000',
        ]));
    }

    #[OA\Post(
        path: '/v0/customers/assets/withdrawals',
        summary: 'Request an asset withdrawal',
        tags: ['Assets'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['asset_wallet_id', 'amount'],
                properties: [
                    new OA\Property(
                        property: 'asset_wallet_id',
                        description: 'The wallet_reference UUID returned by GET /v0/customers/assets/wallets.',
                        type: 'string',
                        format: 'uuid'
                    ),
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
