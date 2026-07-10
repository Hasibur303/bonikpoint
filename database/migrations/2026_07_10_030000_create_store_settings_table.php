<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('store_settings')->insert([
            ['key' => 'inside_dhaka_delivery_charge', 'value' => '60', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'outside_dhaka_delivery_charge', 'value' => '120', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bkash_number', 'value' => '01832510343', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'nagad_number', 'value' => '01832510343', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'rocket_number', 'value' => '018325103435', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'delivery_pay_later_note_bn', 'value' => 'ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত আপনার অর্ডার সম্পূর্ণভাবে কনফার্ম হবে না।', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
