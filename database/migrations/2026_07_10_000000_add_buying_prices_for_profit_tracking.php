<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('buying_price', 10, 2)->default(0);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('buying_price', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('buying_price');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('buying_price');
        });
    }
};
