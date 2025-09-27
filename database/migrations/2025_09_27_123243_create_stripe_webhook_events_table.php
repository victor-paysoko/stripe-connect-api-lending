<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::create('stripe_webhook_events', function (Blueprint $t) {
            $t->id();
            $t->string('event_id')->unique(); // evt_...
            $t->timestamp('processed_at')->useCurrent();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('stripe_webhook_events');
    }
};
