<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadOptimizer
{
    private const RESPONSIVE_WIDTHS = [160, 480, 960];

    public static function store(UploadedFile $file, string $directory, int $maxDimension = 1600): string
    {
        if (! self::supported()) {
            return $file->store($directory, 'public');
        }

        $sourceData = @file_get_contents($file->getRealPath());
        $source = $sourceData ? @imagecreatefromstring($sourceData) : false;

        if (! $source) {
            return $file->store($directory, 'public');
        }

        $filename = pathinfo($file->hashName(), PATHINFO_FILENAME).'.webp';
        $path = trim($directory, '/').'/'.$filename;

        if (! self::writeResponsiveImages($source, $path, $maxDimension)) {
            imagedestroy($source);

            return $file->store($directory, 'public');
        }

        imagedestroy($source);

        return $path;
    }

    public static function optimizeExisting(string $path, int $maxDimension = 1600): bool
    {
        if (! self::supported()) {
            return false;
        }

        $sourcePath = self::preferredSourcePath($path);
        if (! $sourcePath) {
            return false;
        }

        $sourceData = @file_get_contents($sourcePath);
        $source = $sourceData ? @imagecreatefromstring($sourceData) : false;

        if (! $source) {
            return false;
        }

        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path);
        $optimized = self::writeResponsiveImages($source, $webpPath, $maxDimension);
        imagedestroy($source);

        return $optimized;
    }

    public static function supported(): bool
    {
        return function_exists('imagecreatefromstring') && function_exists('imagewebp');
    }

    private static function writeResponsiveImages($source, string $path, int $maxDimension): bool
    {
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $baseScale = min(1, $maxDimension / max($sourceWidth, $sourceHeight));
        $baseWidth = max(1, (int) round($sourceWidth * $baseScale));
        $baseHeight = max(1, (int) round($sourceHeight * $baseScale));

        $baseContents = self::encode($source, $baseWidth, $baseHeight, 80);
        if (! $baseContents || ! self::put($path, $baseContents)) {
            return false;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $basePath = substr($path, 0, -strlen($extension) - 1);

        foreach (self::RESPONSIVE_WIDTHS as $width) {
            if ($sourceWidth <= $width) {
                continue;
            }

            $height = max(1, (int) round($sourceHeight * ($width / $sourceWidth)));
            $contents = self::encode($source, $width, $height, $width <= 480 ? 76 : 78);

            if ($contents) {
                self::put($basePath.'-'.$width.'.webp', $contents);
            }
        }

        return true;
    }

    private static function encode($source, int $width, int $height, int $quality): string|false
    {
        $target = imagecreatetruecolor($width, $height);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $width, $height, $transparent);
        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $width,
            $height,
            imagesx($source),
            imagesy($source)
        );

        ob_start();
        $encoded = imagewebp($target, null, $quality);
        $contents = ob_get_clean();
        imagedestroy($target);

        return $encoded && $contents ? $contents : false;
    }

    private static function put(string $path, string $contents): bool
    {
        if (! str_starts_with($path, 'assets/')) {
            return Storage::disk('public')->put($path, $contents);
        }

        $absolutePath = public_path($path);
        $directory = dirname($absolutePath);

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            return false;
        }

        return file_put_contents($absolutePath, $contents) !== false;
    }

    private static function preferredSourcePath(string $path): ?string
    {
        $webpPath = preg_replace('/\.(?:jpe?g|png)$/i', '.webp', $path);

        foreach (array_unique([$webpPath, $path]) as $candidate) {
            $absolutePath = str_starts_with($candidate, 'assets/')
                ? public_path($candidate)
                : Storage::disk('public')->path($candidate);

            if (is_file($absolutePath)) {
                return $absolutePath;
            }
        }

        return null;
    }
}
