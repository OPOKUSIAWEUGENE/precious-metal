<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('callbacks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // INCOMING, OUTGOING
            $table->string('status')->default('PENDING'); // PENDING, SENT, FAILED, DELIVERED
            $table->string('endpoint')->nullable();
            $table->string('method', 10)->default('POST');
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->json('headers')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('related_type')->nullable();
            $table->uuid('related_id')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index(['related_type', 'related_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('callbacks');
    }
};