<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::create('loan_installments', function (Blueprint $t) {
            $t->id();
            $t->string('loan_id');
            $t->string('borrower_id');
            $t->bigInteger('amount_cents');
            $t->string('currency', 3)->default('usd');
            $t->string('fi_account_id');                 // acct_...
            $t->string('stripe_payment_intent_id')->nullable();
            $t->string('stripe_charge_id')->nullable();
            $t->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();

            $t->index(['loan_id', 'borrower_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('loan_installments');
    }
};
