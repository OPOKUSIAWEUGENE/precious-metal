<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('start_stop_id');
            $table->uuid('end_stop_id');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'ARCHIVED'])->default('ACTIVE');
            $table->enum('route_type', ['INTRACITY', 'INTERCITY']);
            $table->timestamps();

            $table->foreign('start_stop_id')->references('id')->on('route_stops')->onDelete('cascade');
            $table->foreign('end_stop_id')->references('id')->on('route_stops')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};