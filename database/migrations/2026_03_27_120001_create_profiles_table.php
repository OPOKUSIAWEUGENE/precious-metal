<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('profile_type', ['PLATFORM_OPERATOR', 'CLIENT_OPERATOR', 'PASSENGER', 'DRIVER']);
            $table->uuid('role_id')->nullable();
            $table->uuid('operator_id')->nullable();
            $table->uuid('user_id');
            $table->boolean('active')->default(true);
            $table->boolean('current')->default(true);
            $table->string('license')->nullable();
            $table->enum('driver_status', ['AVAILABLE', 'ON_TRIP', 'OFFLINE', 'BUSY'])->nullable()->default('AVAILABLE');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};