<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('description');
            }

            if (! Schema::hasColumn('products', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }

            if (! Schema::hasColumn('products', 'image_alt')) {
                $table->string('image_alt')->nullable()->after('image');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('description');
            }

            if (! Schema::hasColumn('categories', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }

            if (! Schema::hasColumn('categories', 'image_alt')) {
                $table->string('image_alt')->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = collect(['seo_title', 'seo_description', 'image_alt'])
                ->filter(fn (string $column) => Schema::hasColumn('products', $column))
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            $columns = collect(['seo_title', 'seo_description', 'image_alt'])
                ->filter(fn (string $column) => Schema::hasColumn('categories', $column))
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
