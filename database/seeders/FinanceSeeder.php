<?php

namespace Database\Seeders;

use App\Modules\v0\Finances\Models\Settlement;
use App\Modules\v0\Finances\Models\Commission;
use App\Modules\v0\Finances\Models\Payment;
use App\Modules\v0\Finances\Enums\SettlementStatus;
use App\Modules\v0\Finances\Enums\CommissionStatus;
use App\Modules\v0\Finances\Enums\PaymentStatus;
use App\Modules\v0\Finances\Enums\PaymentMethod;
use App\Modules\v0\Trips\Models\Trip;
use App\Modules\v0\Trips\Models\Booking;
use App\Modules\v0\Users\Models\Profile;
use App\Modules\v0\Users\Enums\ProfileType;
use App\Modules\v0\Operators\Models\Operator;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        // Get sample data
        $trips = Trip::all();
        $bookings = Booking::all();
        $driverProfiles = Profile::where('profile_type', ProfileType::DRIVER)->where('active', true)->get();
        $passengerProfiles = Profile::where('profile_type', ProfileType::PASSENGER)->where('active', true)->get();
        $operators = Operator::all();

        if ($operators->isEmpty() || $driverProfiles->isEmpty()) {
            $this->command->warn('Please run UserSeeder and OperatorSeeder first.');
            return;
        }

        // Create sample payments
        if ($bookings->isNotEmpty() && $passengerProfiles->isNotEmpty()) {
            $payments = [
                [
                    'booking_id' => $bookings->first()->id,
                    'passenger_profile_id' => $bookings->first()->passenger_profile_id,
                    'operator_id' => $trips->first()?->operator_id ?? $operators->first()->id,
                    'amount' => $bookings->first()->total_price ?? 50.00,
                    'currency' => 'GHS',
                    'payment_method' => PaymentMethod::MOBILE_MONEY,
                    'status' => PaymentStatus::COMPLETED,
                    'transaction_id' => 'HT-' . now()->timestamp . '-001',
                    'gateway' => 'HUBTEL',
                    'paid_at' => now()->subHours(2),
                ],
                [
                    'booking_id' => $bookings->skip(1)->first()?->id ?? $bookings->first()->id,
                    'passenger_profile_id' => $bookings->skip(1)->first()?->passenger_profile_id ?? $passengerProfiles->first()->id,
                    'operator_id' => $operators->first()->id,
                    'amount' => 25.00,
                    'currency' => 'GHS',
                    'payment_method' => PaymentMethod::CARD,
                    'status' => PaymentStatus::COMPLETED,
                    'transaction_id' => 'HT-' . now()->timestamp . '-002',
                    'gateway' => 'HUBTEL',
                    'paid_at' => now()->subDays(1),
                ],
            ];

            foreach ($payments as $paymentData) {
                Payment::firstOrCreate(
                    ['transaction_id' => $paymentData['transaction_id']],
                    $paymentData
                );
            }
        }

        // Create sample commissions
        if ($trips->isNotEmpty()) {
            $commissions = [
                [
                    'trip_id' => $trips->first()->id,
                    'operator_id' => $trips->first()->operator_id,
                    'driver_profile_id' => $trips->first()->driver_profile_id,
                    'amount' => ($trips->first()->price ?? 50) * 0.10,
                    'rate' => 0.10,
                    'gross_amount' => $trips->first()->price ?? 50,
                    'net_amount' => ($trips->first()->price ?? 50) * 0.90,
                    'status' => CommissionStatus::PENDING,
                    'period_start' => now()->startOfMonth(),
                    'period_end' => now()->endOfMonth(),
                ],
            ];

            foreach ($commissions as $commissionData) {
                Commission::firstOrCreate(
                    [
                        'trip_id' => $commissionData['trip_id'],
                        'driver_profile_id' => $commissionData['driver_profile_id'],
                    ],
                    $commissionData
                );
            }
        }

        // Create sample settlements
        $settlements = [
            [
                'operator_id' => $operators->first()->id,
                'driver_profile_id' => $driverProfiles->first()->id,
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'total_amount' => 5000.00,
                'commission_amount' => 500.00,
                'net_amount' => 4500.00,
                'status' => SettlementStatus::PENDING,
                'payment_method' => 'MOBILE_MONEY',
            ],
            [
                'operator_id' => $operators->skip(1)->first()?->id ?? $operators->first()->id,
                'driver_profile_id' => $driverProfiles->skip(1)->first()?->id ?? $driverProfiles->first()->id,
                'period_start' => now()->subMonth()->startOfMonth(),
                'period_end' => now()->subMonth()->endOfMonth(),
                'total_amount' => 3500.00,
                'commission_amount' => 350.00,
                'net_amount' => 3150.00,
                'status' => SettlementStatus::COMPLETED,
                'payment_method' => 'BANK_TRANSFER',
                'payment_reference' => 'TRF-' . now()->subMonth()->timestamp,
                'processed_at' => now()->subDays(15),
            ],
        ];

        foreach ($settlements as $settlementData) {
            Settlement::firstOrCreate(
                [
                    'operator_id' => $settlementData['operator_id'],
                    'driver_profile_id' => $settlementData['driver_profile_id'],
                    'period_start' => $settlementData['period_start'],
                    'period_end' => $settlementData['period_end'],
                ],
                $settlementData
            );
        }

        $this->command->info('Finance (Payments, Commissions, Settlements) seeded successfully!');
    }
}