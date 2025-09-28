<?php
// database/migrations/2024_01_15_000002_create_processed_webhook_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('processed_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique(); // Stripe event ID
            $table->string('type'); // Stripe event type
            $table->timestamp('processed_at')->useCurrent();

            $table->index(['type', 'processed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('processed_webhook_events');
    }
};
