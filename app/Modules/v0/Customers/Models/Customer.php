<?php

namespace App\Modules\v0\Customers\Models;

use App\Traits\BootTrait;
use App\Modules\v0\Assets\Models\AssetWallet;
use App\Modules\v0\Assets\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;
use Laravel\Sanctum\HasApiTokens;

class Customer extends AuthenticatableUser
{
    use HasApiTokens, HasFactory, BootTrait;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'email',
        'password',
        'first_name',
        'last_name',
        'date_of_birth',
        'nationality',
        'resident_address',
        'selfie',
        'phone_number',
        'kyc',
        'card_details',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'active' => 'boolean',
            'date_of_birth' => 'date',
            'kyc' => 'array',
            'card_details' => 'array',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::orderedUuid();
            }
        });

        static::created(function (self $customer): void {
            $customer->assetWallets()->firstOrCreate(
                [
                    'weight_unit' => 'gram',
                    'purity' => '0.999900',
                ],
                [
                    'available_weight' => 0,
                    'reserved_weight' => 0,
                    'status' => 'active',
                ]
            );
        });
    }

    public function assetWallets(): MorphMany
    {
        return $this->morphMany(AssetWallet::class, 'owner');
    }

    public function withdrawals(): MorphMany
    {
        return $this->morphMany(Withdrawal::class, 'owner');
    }
}
