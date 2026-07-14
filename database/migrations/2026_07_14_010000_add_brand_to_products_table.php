<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'brand')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('brand')->nullable()->after('name')->index();
            });
        }

        foreach ([
            'atvs-genx-rechargeable-pod-device' => 'ATVS',
            'smok-vape-pen-22' => 'SMOK',
            'rincoe-manto-nano-a4' => 'Rincoe',
        ] as $slug => $brand) {
            DB::table('products')->where('slug', $slug)->whereNull('brand')->update(['brand' => $brand]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'brand')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('brand');
            });
        }
    }
};
