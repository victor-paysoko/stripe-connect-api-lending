<?php
// database/migrations/2024_01_15_000004_create_borrowers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('financial_institution_id');
            $table->string('external_borrower_id'); // FI's borrower ID

            // Customer info
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();

            // Stripe customer
            $table->string('stripe_customer_id')->nullable()->index();

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Unique constraint - one stripe customer per FI borrower
            $table->unique(['financial_institution_id', 'external_borrower_id']);
            $table->index(['financial_institution_id', 'email']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('borrowers');
    }
};
