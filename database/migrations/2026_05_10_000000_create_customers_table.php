<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('resident_address')->nullable();
            $table->string('selfie')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->json('kyc')->nullable();
            $table->json('card_details')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('email');
            $table->index('phone_number');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
