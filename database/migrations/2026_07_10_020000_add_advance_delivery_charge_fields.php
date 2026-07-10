<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('advance_delivery_charge')->default(true);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('advance_delivery_required')->default(false);
            $table->string('delivery_area')->nullable();
            $table->string('delivery_charge_payment_option')->nullable();
            $table->string('delivery_payment_method')->nullable();
            $table->string('delivery_payment_mobile')->nullable();
            $table->string('delivery_transaction_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'advance_delivery_required',
                'delivery_area',
                'delivery_charge_payment_option',
                'delivery_payment_method',
                'delivery_payment_mobile',
                'delivery_transaction_id',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('advance_delivery_charge');
        });
    }
};
