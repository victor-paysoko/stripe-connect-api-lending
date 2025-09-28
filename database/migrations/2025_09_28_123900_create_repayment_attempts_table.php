<?php
// database/migrations/2024_01_15_000001_create_repayment_attempts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('repayment_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('financial_institution_id'); // Your FI ID
            $table->string('loan_id'); // FI's loan ID
            $table->string('borrower_id'); // FI's borrower ID
            $table->integer('installment_number');
            $table->integer('amount_due'); // in cents
            $table->string('currency', 3)->default('usd');

            // Stripe IDs
            $table->string('stripe_payment_intent_id')->nullable()->index();
            $table->string('stripe_transfer_id')->nullable();

            // Status tracking
            $table->enum('status', [
                'pending',
                'requires_action',
                'processing',
                'succeeded',
                'failed',
                'canceled'
            ])->default('pending');

            $table->text('failure_reason')->nullable();

            // Idempotency
            $table->string('idempotency_key')->unique();

            // Metadata
            $table->json('metadata')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes for query performance
            $table->index(['financial_institution_id', 'loan_id']);
            $table->index(['financial_institution_id', 'borrower_id']);
            $table->index(['status', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('repayment_attempts');
    }
};
