<?php
// database/migrations/2024_01_15_000003_create_financial_institutions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('financial_institutions', function (Blueprint $table) {
            $table->string('id')->primary(); // Your FI ID
            $table->string('name');
            $table->string('stripe_account_id')->nullable()->unique(); // acct_xxx
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_institutions');
    }
};
