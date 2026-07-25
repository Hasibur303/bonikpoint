<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('vape_device_type')->nullable()->after('brand');
        });

        Schema::create('product_flavors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('selected_flavor_name', 100)->nullable()->after('selected_color_hex');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('selected_flavor_name');
        });

        Schema::dropIfExists('product_flavors');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('vape_device_type');
        });
    }
};
