<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('warranty_type')->default('none')->after('advance_delivery_charge');
            $table->string('warranty_duration')->nullable()->after('warranty_type');
            $table->text('warranty_details')->nullable()->after('warranty_duration');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['warranty_type', 'warranty_duration', 'warranty_details']);
        });
    }
};
