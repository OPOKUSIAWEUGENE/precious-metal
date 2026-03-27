<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fleet_identifier')->unique();
            $table->uuid('operator_id');
            $table->string('type');
            $table->string('plate_number');
            $table->integer('capacity');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'MAINTENANCE', 'RETIRED'])->default('ACTIVE');
            $table->uuid('created_by');
            $table->uuid('updated_by');
            $table->timestamps();

            $table->foreign('operator_id')->references('id')->on('operators')->onDelete('cascade');
            $table->index('operator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};