<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('steadfast_consignment_id', 120)->nullable()->unique()->after('parcel_id');
            $table->string('steadfast_tracking_code', 120)->nullable()->unique()->after('steadfast_consignment_id');
            $table->string('steadfast_status', 80)->nullable()->after('steadfast_tracking_code');
            $table->decimal('steadfast_cod_amount', 10, 2)->nullable()->after('steadfast_status');
            $table->timestamp('steadfast_submitted_at')->nullable()->after('steadfast_cod_amount');
            $table->timestamp('steadfast_last_synced_at')->nullable()->after('steadfast_submitted_at');
            $table->text('steadfast_last_error')->nullable()->after('steadfast_last_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['steadfast_consignment_id']);
            $table->dropUnique(['steadfast_tracking_code']);
            $table->dropColumn([
                'steadfast_consignment_id',
                'steadfast_tracking_code',
                'steadfast_status',
                'steadfast_cod_amount',
                'steadfast_submitted_at',
                'steadfast_last_synced_at',
                'steadfast_last_error',
            ]);
        });
    }
};
