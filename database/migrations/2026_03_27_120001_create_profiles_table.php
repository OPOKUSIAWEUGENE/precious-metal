<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('profile_type'); // PLATFORM_OPERATOR, CLIENT_OPERATOR, PASSENGER, DRIVER
            $table->uuid('role_id')->nullable();
            $table->uuid('operator_id')->nullable(); // Only for CLIENT_OPERATOR profile type
            $table->uuid('user_id');
            $table->boolean('active')->default(true);
            $table->boolean('current')->default(true);
            $table->string('license')->nullable(); // For DRIVER profile type
            $table->string('driver_status')->default('AVAILABLE'); // AVAILABLE, ON_TRIP, OFF_DUTY, ON_BREAK, SUSPENDED
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('profile_type');
            $table->index('user_id');
            $table->index('active');
            $table->index('current');
            $table->index('driver_status');
            
            // Unique constraint: a user can only have one profile of each type as current
            $table->unique(['user_id', 'profile_type', 'current']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};