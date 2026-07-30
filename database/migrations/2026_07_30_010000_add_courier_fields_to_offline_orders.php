<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('requires_courier')->default(false)->after('is_offline_sale');
            $table->boolean('offline_payment_collected')->default(true)->after('requires_courier');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['requires_courier', 'offline_payment_collected']);
        });
    }
};
