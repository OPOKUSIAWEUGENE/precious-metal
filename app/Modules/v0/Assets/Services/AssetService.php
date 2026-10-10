<?php

namespace App\Modules\v0\Assets\Services;

use App\Modules\BaseService;
use App\Modules\v0\Assets\Repositories\AssetWalletRepository;
use App\Modules\v0\Assets\Repositories\WalletQuoteRepository;
use App\Modules\v0\Assets\Repositories\WalletTransactionRepository;
use App\Modules\v0\Assets\Repositories\WithdrawalRepository;
use App\Modules\v0\Assets\Resources\AssetWalletResource;
use App\Modules\v0\Assets\Resources\WalletQuoteResource;
use App\Modules\v0\Assets\Resources\WalletTransactionResource;
use App\Modules\v0\Assets\Resources\WithdrawalResource;
use App\Modules\v0\Assets\Models\WalletQuote;
use App\Modules\v0\Assets\Models\WalletTransaction;
use App\Modules\v0\Customers\Models\Customer;
use App\Modules\v0\Fees\Repositories\FeeRepository;
use App\Modules\v0\Rates\Repositories\RateRepository;
use App\Modules\v0\Users\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssetService extends BaseService
{
    use ApiResponse;

    public function __construct(
        protected AssetWalletRepository $walletRepository,
        protected WalletQuoteRepository $quoteRepository,
        protected WalletTransactionRepository $transactionRepository,
        protected WithdrawalRepository $withdrawalRepository,
        protected RateRepository $rateRepository,
        protected FeeRepository $feeRepository,
    ) {
    }

    public function myWallets(): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        return self::success(
            AssetWalletResource::collection($this->walletRepository->forOwner($owner))->response()->getData(true)
        );
    }

    public function allWallets(int $perPage = 15): JsonResponse
    {
        $actor = Auth::user();

        if (!$actor instanceof User || !$actor->isPlatformAdmin()) {
            return self::error('Forbidden', code: 403);
        }

        return self::success(
            AssetWalletResource::collection($this->walletRepository->paginateAll($perPage))->response()->getData(true)
        );
    }

    public function currentRate(?string $rateType = null): JsonResponse
    {
        if ($rateType !== null && ! in_array($rateType, ['buying', 'selling'], true)) {
            return self::error('Invalid rate type', code: 422);
        }

        $rates = $this->rateRepository->currentActiveByType($rateType);

        if ($rateType !== null) {
            $rate = $rates->get($rateType);

            if (! $rate) {
                return self::error('Active rate not found', code: 404);
            }

            return self::success([
                'rates' => [
                    $rateType => $this->formatRate($rate),
                ],
            ]);
        }

        if ($rates->isEmpty()) {
            return self::error('Active rate not found', code: 404);
        }

        return self::success([
            'rates' => $rates->map(fn ($rate) => $this->formatRate($rate)),
        ]);
    }

    public function currentPrice(): JsonResponse
    {
        $buying = $this->rateRepository->currentActive('buying');
        $selling = $this->rateRepository->currentActive('selling');

        if (!$buying || !$selling) {
            return self::error('Price data not available', code: 404);
        }

        return self::success([
            'currency' => 'GHS',
            'buy_price_per_gram' => $buying->rate,
            'sell_price_per_gram' => $selling->rate,
            'market_price_per_gram' => number_format(((float) $buying->rate + (float) $selling->rate) / 2, 4, '.', ''),
            'effective_at' => $this->latestTimestamp($buying->effective_at, $selling->effective_at),
        ]);
    }

    public function myBalance(): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $buying = $this->rateRepository->currentActive('buying');
        $wallets = $this->walletRepository->forOwner($owner);

        return self::success([
            'wallets' => $wallets->map(function ($wallet) use ($buying) {
                $available = (float) $wallet->available_weight;
                $reserved = (float) $wallet->reserved_weight;
                $total = $available + $reserved;
                $price = $buying ? (float) $buying->rate : 0;

                return [
                    'id' => $wallet->id,
                    'wallet_reference' => $wallet->wallet_reference,
                    'available_weight' => $wallet->available_weight,
                    'reserved_weight' => $wallet->reserved_weight,
                    'total_weight' => number_format($total, 8, '.', ''),
                    'current_price_per_gram' => $buying?->rate,
                    'estimated_value' => number_format($total * $price, 2, '.', ''),
                    'weight_unit' => $wallet->weight_unit,
                    'status' => $wallet->status,
                ];
            }),
        ]);
    }

    public function myTransactions(int $perPage = 15, array $filters = []): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $query = WalletTransaction::query()
            ->where('owner_type', $owner::class)
            ->where('owner_id', $owner->getAuthIdentifier())
            ->orderByDesc('created_at');

        if (!empty($filters['operation'])) {
            $query->where('operation', $filters['operation']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return self::success(
            WalletTransactionResource::collection($query->paginate($perPage))->response()->getData(true)
        );
    }

    public function transactionByReference(string $reference): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $transaction = WalletTransaction::query()
            ->where('reference', $reference)
            ->where('owner_type', $owner::class)
            ->where('owner_id', $owner->getAuthIdentifier())
            ->first();

        if (!$transaction) {
            return self::error('Transaction not found', code: 404);
        }

        return self::success(new WalletTransactionResource($transaction));
    }

    public function allTransactions(int $perPage = 15): JsonResponse
    {
        $actor = Auth::user();

        if (!$actor instanceof User || !$actor->isPlatformAdmin()) {
            return self::error('Forbidden', code: 403);
        }

        return self::success(
            WalletTransactionResource::collection($this->transactionRepository->paginateAll($perPage))->response()->getData(true)
        );
    }

    public function myQuotes(int $perPage = 15): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        return self::success(
            WalletQuoteResource::collection(
                WalletQuote::query()
                    ->where('created_by_type', $owner::class)
                    ->where('created_by_id', $owner->getAuthIdentifier())
                    ->orderByDesc('created_at')
                    ->paginate($perPage)
            )->response()->getData(true)
        );
    }

    public function allQuotes(int $perPage = 15): JsonResponse
    {
        $actor = Auth::user();

        if (!$actor instanceof User || !$actor->isPlatformAdmin()) {
            return self::error('Forbidden', code: 403);
        }

        return self::success(
            WalletQuoteResource::collection($this->quoteRepository->paginateAll($perPage))->response()->getData(true)
        );
    }

    public function quoteByReference(string $reference): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $quote = $this->quoteRepository->findByReference($reference);

        if (!$quote) {
            return self::error('Quote not found', code: 404);
        }

        return self::success(new WalletQuoteResource($quote));
    }

    public function createQuote(array $data): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $operation = $data['operation'] ?? null;
        if (!in_array($operation, ['buy', 'sell', 'transfer'], true)) {
            return self::error('Invalid quote operation', code: 422);
        }

        $pricing = $this->pricingSnapshot();
        $feeRate = $this->currentFeeRate();
        $quoteData = match ($operation) {
            'buy' => $this->buildBuyQuote($data, $pricing, $feeRate, $owner),
            'sell' => $this->buildSellQuote($data, $pricing, $feeRate, $owner),
            'transfer' => $this->buildTransferQuote($data, $feeRate, $owner),
        };

        if (($quoteData['status'] ?? null) === 'failed') {
            return self::error('Invalid quote payload', code: 422);
        }

        $quote = $this->quoteRepository->create($quoteData);

        return self::success(new WalletQuoteResource($quote), 201);
    }

    public function recipientEnquiry(array $data): JsonResponse
    {
        $recipient = $this->resolveOwnerByIdentifier($data['identifier_type'] ?? '', $data['identifier'] ?? '');

        if (!$recipient) {
            return self::error('Recipient not found', code: 404);
        }

        return self::success([
            'recipient_type' => $recipient['type'],
            'recipient_id' => $recipient['id'],
            'recipient_name' => trim(($recipient['first_name'] ?? '') . ' ' . ($recipient['last_name'] ?? '')),
            'masked_phone' => $this->maskPhone($recipient['phone_number'] ?? ''),
        ]);
    }

    public function executeTransfer(array $data): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $senderWallet = $this->walletRepository->forOwner($owner)->first();
        if (!$senderWallet) {
            return self::error('Sender wallet not found', code: 404);
        }

        $recipient = $this->resolveOwnerById((string) ($data['recipient_id'] ?? ''));
        if (!$recipient) {
            return self::error('Recipient not found', code: 404);
        }

        $quote = $this->quoteRepository->findByReference((string) ($data['quote_reference'] ?? ''));
        if (!$quote || $quote->operation !== 'transfer') {
            return self::error('Transfer quote not found', code: 404);
        }

        $goldWeight = (float) $quote->gold_weight;
        $feeWeight = isset($quote->metadata['fee_weight']) ? (float) $quote->metadata['fee_weight'] : 0.0;
        $totalDebit = $goldWeight + $feeWeight;

        if ((float) $senderWallet->available_weight < $totalDebit) {
            return self::error('Insufficient wallet balance', code: 422);
        }

        $recipientWallet = $this->resolveWalletForOwner($recipient['model']);

        $groupReference = 'TRN-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        DB::transaction(function () use ($senderWallet, $recipientWallet, $owner, $recipient, $quote, $goldWeight, $feeWeight, $totalDebit, $groupReference, $data): void {
            $senderWallet->available_weight = (float) $senderWallet->available_weight - $totalDebit;
            $senderWallet->last_transaction_at = now();
            $senderWallet->save();

            $recipientWallet->available_weight = (float) $recipientWallet->available_weight + $goldWeight;
            $recipientWallet->last_transaction_at = now();
            $recipientWallet->save();

            $this->transactionRepository->create([
                'group_reference' => $groupReference,
                'operation' => 'transfer',
                'direction' => 'debit',
                'status' => 'completed',
                'wallet_id' => $senderWallet->id,
                'owner_type' => $owner::class,
                'owner_id' => $owner->getAuthIdentifier(),
                'counterparty_type' => $recipient['type'],
                'counterparty_id' => $recipient['id'],
                'quote_reference' => $quote->reference,
                'asset_amount' => $totalDebit,
                'fee_amount' => $feeWeight,
                'narration' => $data['narration'] ?? null,
                'metadata' => ['recipient_id' => $recipient['id']],
                'completed_at' => now(),
            ]);

            $this->transactionRepository->create([
                'group_reference' => $groupReference,
                'operation' => 'transfer',
                'direction' => 'credit',
                'status' => 'completed',
                'wallet_id' => $recipientWallet->id,
                'owner_type' => $recipient['type'],
                'owner_id' => $recipient['id'],
                'counterparty_type' => $owner::class,
                'counterparty_id' => $owner->getAuthIdentifier(),
                'quote_reference' => $quote->reference,
                'asset_amount' => $goldWeight,
                'fee_amount' => 0,
                'narration' => $data['narration'] ?? null,
                'metadata' => ['sender_id' => $owner->getAuthIdentifier()],
                'completed_at' => now(),
            ]);
        });

        return self::success([
            'reference' => $groupReference,
            'status' => 'completed',
        ], 201);
    }

    public function transferQuote(array $data): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $recipient = $this->resolveOwnerById((string) ($data['recipient_id'] ?? ''));
        if (!$recipient) {
            return self::error('Recipient not found', code: 404);
        }

        $pricing = $this->pricingSnapshot();
        $feeRate = $this->currentFeeRate();
        $goldWeight = (float) ($data['gold_weight'] ?? 0);
        if ($goldWeight <= 0) {
            return self::error('Gold weight must be greater than zero', code: 422);
        }

        $feeWeight = $goldWeight * $feeRate;
        $quote = $this->quoteRepository->create([
            'operation' => 'transfer',
            'status' => 'active',
            'currency' => 'GHS',
            'gold_weight' => $goldWeight,
            'price_per_gram' => $pricing['buy_price_per_gram'],
            'fee_rate' => $feeRate * 100,
            'fee_amount' => $feeWeight,
            'total_payable' => null,
            'total_receivable' => null,
            'recipient_type' => $recipient['type'],
            'recipient_id' => $recipient['id'],
            'expires_at' => now()->addMinutes(5),
            'metadata' => ['fee_weight' => $feeWeight],
            'created_by_type' => $owner::class,
            'created_by_id' => $owner->getAuthIdentifier(),
        ]);

        return self::success(new WalletQuoteResource($quote), 201);
    }

    public function saleQuote(array $data): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $goldWeight = (float) ($data['gold_weight'] ?? 0);
        if ($goldWeight <= 0) {
            return self::error('Gold weight must be greater than zero', code: 422);
        }

        $pricing = $this->pricingSnapshot();
        $feeRate = $this->currentFeeRate();
        $gross = $goldWeight * (float) $pricing['sell_price_per_gram'];
        $feeAmount = $gross * $feeRate;
        $net = $gross - $feeAmount;

        $quote = $this->quoteRepository->create([
            'operation' => 'sell',
            'status' => 'active',
            'currency' => $data['settlement_currency'] ?? 'GHS',
            'gold_weight' => $goldWeight,
            'price_per_gram' => $pricing['sell_price_per_gram'],
            'fee_rate' => $feeRate * 100,
            'fee_amount' => $feeAmount,
            'total_receivable' => $net,
            'expires_at' => now()->addMinutes(5),
            'metadata' => ['gross_cash_amount' => $gross],
            'created_by_type' => $owner::class,
            'created_by_id' => $owner->getAuthIdentifier(),
        ]);

        return self::success(new WalletQuoteResource($quote), 201);
    }

    public function executeSale(array $data): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $wallet = $this->walletRepository->forOwner($owner)->first();
        if (!$wallet) {
            return self::error('Wallet not found', code: 404);
        }

        $quote = $this->quoteRepository->findByReference((string) ($data['quote_reference'] ?? ''));
        if (!$quote || $quote->operation !== 'sell') {
            return self::error('Sell quote not found', code: 404);
        }

        $goldWeight = (float) $quote->gold_weight;
        $feeAmount = (float) $quote->fee_amount;

        if ((float) $wallet->available_weight < $goldWeight) {
            return self::error('Insufficient wallet balance', code: 422);
        }

        DB::transaction(function () use ($wallet, $owner, $quote, $goldWeight, $feeAmount, $data): void {
            $wallet->available_weight = (float) $wallet->available_weight - $goldWeight;
            $wallet->last_transaction_at = now();
            $wallet->save();

            $this->transactionRepository->create([
                'group_reference' => 'SAL-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6)),
                'operation' => 'sell',
                'direction' => 'debit',
                'status' => 'completed',
                'wallet_id' => $wallet->id,
                'owner_type' => $owner::class,
                'owner_id' => $owner->getAuthIdentifier(),
                'quote_reference' => $quote->reference,
                'asset_amount' => $goldWeight,
                'cash_amount' => $quote->total_receivable,
                'price_per_gram' => $quote->price_per_gram,
                'fee_amount' => $feeAmount,
                'total_amount' => $quote->total_receivable,
                'narration' => $data['narration'] ?? null,
                'metadata' => ['credit_destination' => $data['credit_destination'] ?? 'cash_wallet'],
                'completed_at' => now(),
            ]);
        });

        return self::success([
            'reference' => $quote->reference,
            'status' => 'completed',
        ], 201);
    }

    public function purchaseQuote(array $data): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $cashAmount = (float) ($data['cash_amount'] ?? 0);
        if ($cashAmount <= 0) {
            return self::error('Cash amount must be greater than zero', code: 422);
        }

        $pricing = $this->pricingSnapshot();
        $feeRate = $this->currentFeeRate();
        $feeAmount = $cashAmount * $feeRate;
        $goldWeight = $cashAmount / (float) $pricing['buy_price_per_gram'];
        $total = $cashAmount + $feeAmount;

        $quote = $this->quoteRepository->create([
            'operation' => 'buy',
            'status' => 'active',
            'currency' => $data['currency'] ?? 'GHS',
            'cash_amount' => $cashAmount,
            'gold_weight' => $goldWeight,
            'price_per_gram' => $pricing['buy_price_per_gram'],
            'fee_rate' => $feeRate * 100,
            'fee_amount' => $feeAmount,
            'total_payable' => $total,
            'expires_at' => now()->addMinutes(5),
            'created_by_type' => $owner::class,
            'created_by_id' => $owner->getAuthIdentifier(),
        ]);

        return self::success(new WalletQuoteResource($quote), 201);
    }

    public function executePurchase(array $data): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Authenticatable) {
            return self::error('Unauthorized', code: 401);
        }

        $wallet = $this->walletRepository->forOwner($owner)->first();
        if (!$wallet) {
            return self::error('Wallet not found', code: 404);
        }

        $quote = $this->quoteRepository->findByReference((string) ($data['quote_reference'] ?? ''));
        if (!$quote || $quote->operation !== 'buy') {
            return self::error('Purchase quote not found', code: 404);
        }

        $goldWeight = (float) $quote->gold_weight;
        DB::transaction(function () use ($wallet, $owner, $quote, $goldWeight, $data): void {
            $wallet->available_weight = (float) $wallet->available_weight + $goldWeight;
            $wallet->last_transaction_at = now();
            $wallet->save();

            $this->transactionRepository->create([
                'group_reference' => 'PUR-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6)),
                'operation' => 'buy',
                'direction' => 'credit',
                'status' => 'completed',
                'wallet_id' => $wallet->id,
                'owner_type' => $owner::class,
                'owner_id' => $owner->getAuthIdentifier(),
                'quote_reference' => $quote->reference,
                'asset_amount' => $goldWeight,
                'cash_amount' => $quote->cash_amount,
                'price_per_gram' => $quote->price_per_gram,
                'fee_amount' => $quote->fee_amount,
                'total_amount' => $quote->total_payable,
                'narration' => $data['narration'] ?? null,
                'metadata' => ['payment_method' => $data['payment_method'] ?? 'cash_wallet'],
                'completed_at' => now(),
            ]);
        });

        return self::success([
            'reference' => $quote->reference,
            'status' => 'completed',
        ], 201);
    }

    public function requestWithdrawal(array $data): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Customer) {
            return self::error('Unauthorized', code: 401);
        }

        $wallet = $this->walletRepository->findForOwnerByReference($owner, (string) $data['asset_wallet_id']);

        if (!$wallet || $wallet->owner_type !== $owner::class || $wallet->owner_id !== $owner->getAuthIdentifier()) {
            return self::error('Asset wallet not found', code: 404);
        }

        $rate = $this->rateRepository->currentActive('buying');

        if (!$rate) {
            return self::error('Active rate not found', code: 404);
        }

        if (($data['conversion_type'] ?? 'gold_to_cash') !== 'gold_to_cash') {
            return self::error('Only gold_to_cash withdrawals are supported', code: 422);
        }

        $assetAmount = (float) $data['amount'];
        $available = (float) $wallet->available_weight;

        if ($assetAmount <= 0) {
            return self::error('Withdrawal amount must be greater than zero', code: 422);
        }

        if ($available < $assetAmount) {
            return self::error('Insufficient wallet balance', code: 422);
        }

        $cashAmount = $assetAmount * (float) $rate->rate;

        $withdrawal = DB::transaction(function () use ($wallet, $owner, $rate, $assetAmount, $cashAmount, $data) {
            $wallet->available_weight = (float) $wallet->available_weight - $assetAmount;
            $wallet->reserved_weight = (float) $wallet->reserved_weight + $assetAmount;
            $wallet->last_transaction_at = now();
            $wallet->save();

            return $owner->withdrawals()->create([
                'asset_wallet_id' => $wallet->id,
                'rate_id' => $rate->id,
                'requested_amount' => $assetAmount,
                'asset_amount' => $assetAmount,
                'cash_amount' => $cashAmount,
                'conversion_rate' => $rate->rate,
                'conversion_type' => 'gold_to_cash',
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return self::success(new WithdrawalResource($withdrawal->load(['owner', 'wallet', 'rate'])), 201);
    }

    private function formatRate(object $rate): array
    {
        return [
            'id' => $rate->id,
            'rate_type' => $rate->rate_type,
            'rate' => $rate->rate,
            'status' => $rate->status,
            'effective_at' => $rate->effective_at,
            'notes' => $rate->notes,
        ];
    }

    private function pricingSnapshot(): array
    {
        $buying = $this->rateRepository->currentActive('buying');
        $selling = $this->rateRepository->currentActive('selling');

        return [
            'buy_price_per_gram' => $buying ? (float) $buying->rate : 0,
            'sell_price_per_gram' => $selling ? (float) $selling->rate : 0,
        ];
    }

    private function currentFeeRate(): float
    {
        $fee = $this->feeRepository->currentActive();

        return $fee ? ((float) $fee->fee / 100) : 0.0;
    }

    private function buildBuyQuote(array $data, array $pricing, float $feeRate, Authenticatable $owner): array
    {
        $cashAmount = (float) ($data['cash_amount'] ?? 0);
        if ($cashAmount <= 0) {
            return ['operation' => 'buy', 'status' => 'failed', 'currency' => $data['currency'] ?? 'GHS'];
        }

        $feeAmount = $cashAmount * $feeRate;
        $goldWeight = $pricing['buy_price_per_gram'] > 0 ? $cashAmount / $pricing['buy_price_per_gram'] : 0;

        return [
            'operation' => 'buy',
            'status' => 'active',
            'currency' => $data['currency'] ?? 'GHS',
            'cash_amount' => $cashAmount,
            'gold_weight' => $goldWeight,
            'price_per_gram' => $pricing['buy_price_per_gram'],
            'fee_rate' => $feeRate * 100,
            'fee_amount' => $feeAmount,
            'total_payable' => $cashAmount + $feeAmount,
            'expires_at' => now()->addMinutes(5),
            'created_by_type' => $owner::class,
            'created_by_id' => $owner->getAuthIdentifier(),
        ];
    }

    private function buildSellQuote(array $data, array $pricing, float $feeRate, Authenticatable $owner): array
    {
        $goldWeight = (float) ($data['gold_weight'] ?? 0);
        if ($goldWeight <= 0) {
            return ['operation' => 'sell', 'status' => 'failed', 'currency' => $data['currency'] ?? 'GHS'];
        }

        $gross = $goldWeight * $pricing['sell_price_per_gram'];
        $feeAmount = $gross * $feeRate;

        return [
            'operation' => 'sell',
            'status' => 'active',
            'currency' => $data['currency'] ?? 'GHS',
            'gold_weight' => $goldWeight,
            'price_per_gram' => $pricing['sell_price_per_gram'],
            'fee_rate' => $feeRate * 100,
            'fee_amount' => $feeAmount,
            'total_receivable' => $gross - $feeAmount,
            'expires_at' => now()->addMinutes(5),
            'created_by_type' => $owner::class,
            'created_by_id' => $owner->getAuthIdentifier(),
        ];
    }

    private function buildTransferQuote(array $data, float $feeRate, Authenticatable $owner): array
    {
        $goldWeight = (float) ($data['gold_weight'] ?? 0);
        if ($goldWeight <= 0) {
            return ['operation' => 'transfer', 'status' => 'failed', 'currency' => 'GHS'];
        }

        $feeWeight = $goldWeight * $feeRate;
        $recipient = $this->resolveOwnerById((string) ($data['recipient_id'] ?? ''));

        return [
            'operation' => 'transfer',
            'status' => 'active',
            'currency' => $data['currency'] ?? 'GHS',
            'gold_weight' => $goldWeight,
            'price_per_gram' => $this->pricingSnapshot()['buy_price_per_gram'],
            'fee_rate' => $feeRate * 100,
            'fee_amount' => $feeWeight,
            'recipient_type' => $recipient['type'] ?? null,
            'recipient_id' => $recipient['id'] ?? null,
            'expires_at' => now()->addMinutes(5),
            'metadata' => ['fee_weight' => $feeWeight],
            'created_by_type' => $owner::class,
            'created_by_id' => $owner->getAuthIdentifier(),
        ];
    }

    private function resolveOwnerByIdentifier(string $identifierType, string $identifier): ?array
    {
        $identifierType = strtolower($identifierType);

        if ($identifierType === 'email') {
            if ($user = User::query()->where('email', $identifier)->first()) {
                return ['type' => User::class, 'id' => $user->id, 'model' => $user, 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'phone_number' => $user->phone_number];
            }
            if ($customer = Customer::query()->where('email', $identifier)->first()) {
                return ['type' => Customer::class, 'id' => $customer->id, 'model' => $customer, 'first_name' => $customer->first_name, 'last_name' => $customer->last_name, 'phone_number' => $customer->phone_number];
            }
        }

        if ($identifierType === 'phone') {
            if ($user = User::query()->where('phone_number', $identifier)->first()) {
                return ['type' => User::class, 'id' => $user->id, 'model' => $user, 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'phone_number' => $user->phone_number];
            }
            if ($customer = Customer::query()->where('phone_number', $identifier)->first()) {
                return ['type' => Customer::class, 'id' => $customer->id, 'model' => $customer, 'first_name' => $customer->first_name, 'last_name' => $customer->last_name, 'phone_number' => $customer->phone_number];
            }
        }

        if ($identifierType === 'id') {
            return $this->resolveOwnerById($identifier);
        }

        return null;
    }

    private function resolveOwnerById(string $id): ?array
    {
        if ($user = User::query()->find($id)) {
            return ['type' => User::class, 'id' => $user->id, 'model' => $user, 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'phone_number' => $user->phone_number];
        }

        if ($customer = Customer::query()->find($id)) {
            return ['type' => Customer::class, 'id' => $customer->id, 'model' => $customer, 'first_name' => $customer->first_name, 'last_name' => $customer->last_name, 'phone_number' => $customer->phone_number];
        }

        return null;
    }

    private function resolveWalletForOwner(Authenticatable $owner)
    {
        $wallet = $this->walletRepository->forOwner($owner)->first();

        if ($wallet) {
            return $wallet;
        }

        return $owner->assetWallets()->create([
            'weight_unit' => 'gram',
            'purity' => '0.999900',
            'available_weight' => 0,
            'reserved_weight' => 0,
            'status' => 'active',
        ]);
    }

    private function maskPhone(string $phone): string
    {
        $len = strlen($phone);

        if ($len <= 4) {
            return str_repeat('*', $len);
        }

        return substr($phone, 0, 3) . str_repeat('*', max(0, $len - 6)) . substr($phone, -3);
    }

    private function latestTimestamp($first, $second)
    {
        if ($first && $second) {
            return $first->greaterThan($second) ? $first : $second;
        }

        return $first ?? $second;
    }

    public function allWithdrawals(int $perPage = 15): JsonResponse
    {
        $actor = Auth::user();

        if (!$actor instanceof User || !$actor->isPlatformAdmin()) {
            return self::error('Forbidden', code: 403);
        }

        return self::success(
            WithdrawalResource::collection($this->withdrawalRepository->paginateAll($perPage))->response()->getData(true)
        );
    }

    public function approveWithdrawal(string $id): JsonResponse
    {
        $actor = Auth::user();

        if (!$actor instanceof User || !$actor->isPlatformAdmin()) {
            return self::error('Forbidden', code: 403);
        }

        $withdrawal = $this->withdrawalRepository->findWithRelations($id);

        if (!$withdrawal) {
            return self::error('Withdrawal not found', code: 404);
        }

        if ($withdrawal->status !== 'pending') {
            return self::error('Withdrawal is not pending', code: 422);
        }

        $wallet = $withdrawal->wallet;

        DB::transaction(function () use ($withdrawal, $wallet, $actor) {
            $wallet->reserved_weight = max(0, (float) $wallet->reserved_weight - (float) $withdrawal->asset_amount);
            $wallet->last_transaction_at = now();
            $wallet->save();

            $withdrawal->status = 'approved';
            $withdrawal->approved_by = $actor->id;
            $withdrawal->approved_at = now();
            $withdrawal->save();
        });

        return self::success(new WithdrawalResource($withdrawal->fresh(['owner', 'wallet', 'rate', 'approver'])));
    }
}
