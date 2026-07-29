<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('adjustment_type', 40)->nullable()->after('total');
            $table->decimal('adjustment_value', 12, 2)->default(0)->after('adjustment_type');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('adjustment_value');
            $table->decimal('extra_charge_amount', 12, 2)->default(0)->after('discount_amount');
            $table->string('adjustment_reason', 255)->nullable()->after('extra_charge_amount');
            $table->text('adjustment_note')->nullable()->after('adjustment_reason');
            $table->foreignId('adjusted_by')->nullable()->after('adjustment_note')->constrained('users')->nullOnDelete();
            $table->timestamp('adjusted_at')->nullable()->after('adjusted_by');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('adjusted_by');
            $table->dropColumn([
                'adjustment_type',
                'adjustment_value',
                'discount_amount',
                'extra_charge_amount',
                'adjustment_reason',
                'adjustment_note',
                'adjusted_at',
            ]);
        });
    }
};
