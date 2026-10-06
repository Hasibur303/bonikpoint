<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['product_likes', 'product_visits'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('visitor_hash', 64);
                $table->timestamp('created_at');
                $table->unique(['product_id', 'visitor_hash']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_visits');
        Schema::dropIfExists('product_likes');
    }
};
