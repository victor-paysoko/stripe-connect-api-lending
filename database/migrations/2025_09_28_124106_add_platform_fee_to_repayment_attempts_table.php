<?php
// database/migrations/2024_01_15_000005_add_platform_fee_to_repayment_attempts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('repayment_attempts', function (Blueprint $table) {
            $table->integer('platform_fee_amount')->default(0)->after('amount_due');
            $table->integer('net_amount')->nullable()->after('platform_fee_amount'); // amount_due - platform_fee
        });
    }

    public function down()
    {
        Schema::table('repayment_attempts', function (Blueprint $table) {
            $table->dropColumn(['platform_fee_amount', 'net_amount']);
        });
    }
};
