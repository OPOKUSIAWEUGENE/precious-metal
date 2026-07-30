<?php

namespace App\Modules\v0\Assets\Services;

use App\Modules\BaseService;
use App\Modules\v0\Assets\Repositories\AssetWalletRepository;
use App\Modules\v0\Assets\Repositories\WithdrawalRepository;
use App\Modules\v0\Assets\Resources\AssetWalletResource;
use App\Modules\v0\Assets\Resources\WithdrawalResource;
use App\Modules\v0\Customers\Models\Customer;
use App\Modules\v0\Rates\Repositories\RateRepository;
use App\Modules\v0\Users\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetService extends BaseService
{
    use ApiResponse;

    public function __construct(
        protected AssetWalletRepository $walletRepository,
        protected WithdrawalRepository $withdrawalRepository,
        protected RateRepository $rateRepository,
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

    public function requestWithdrawal(array $data): JsonResponse
    {
        $owner = Auth::user();

        if (!$owner instanceof Customer) {
            return self::error('Unauthorized', code: 401);
        }

        $wallet = $this->walletRepository->find((string) $data['asset_wallet_id']);

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
