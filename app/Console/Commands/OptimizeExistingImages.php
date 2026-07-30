<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Festival;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\ImageUploadOptimizer;
use Illuminate\Console\Command;

class OptimizeExistingImages extends Command
{
    protected $signature = 'images:optimize-existing';

    protected $description = 'Create optimized WebP and responsive variants for existing catalog images';

    public function handle(): int
    {
        if (! ImageUploadOptimizer::supported()) {
            $this->error('PHP GD with WebP support is required. Enable the gd extension and run this command again.');

            return self::FAILURE;
        }

        $paths = collect()
            ->merge(Product::whereNotNull('image')->pluck('image'))
            ->merge(ProductImage::whereNotNull('image')->pluck('image'))
            ->merge(Category::whereNotNull('image')->pluck('image'))
            ->merge(Festival::whereNotNull('banner')->pluck('banner'))
            ->filter()
            ->unique()
            ->values();

        if ($paths->isEmpty()) {
            $this->info('No catalog images need optimization.');

            return self::SUCCESS;
        }

        $optimized = 0;
        $progress = $this->output->createProgressBar($paths->count());
        $progress->start();

        foreach ($paths as $path) {
            $optimized += ImageUploadOptimizer::optimizeExisting($path) ? 1 : 0;
            $progress->advance();
        }

        $progress->finish();
        $this->newLine(2);
        $this->info("Optimized {$optimized} of {$paths->count()} catalog images.");

        return $optimized === $paths->count() ? self::SUCCESS : self::FAILURE;
    }
}
