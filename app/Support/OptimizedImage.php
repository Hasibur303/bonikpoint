<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class OptimizedImage
{
    public static function url(string $path): string
    {
        return self::assetUrl(self::preferredPath($path));
    }

    public static function srcSet(?string $path, array $widths = [160, 480, 960]): string
    {
        if (! $path) {
            return '';
        }

        $preferredPath = self::preferredPath($path);
        $extension = pathinfo($preferredPath, PATHINFO_EXTENSION);
        $basePath = substr($preferredPath, 0, -strlen($extension) - 1);
        $candidates = [];

        foreach ($widths as $width) {
            $width = (int) $width;
            $variantPath = $basePath.'-'.$width.'.webp';

            if ($width > 0 && self::exists($variantPath)) {
                $candidates[$width] = self::assetUrl($variantPath).' '.$width.'w';
            }
        }

        $dimensions = self::dimensions($preferredPath);
        if ($dimensions && ! isset($candidates[$dimensions[0]])) {
            $candidates[$dimensions[0]] = self::assetUrl($preferredPath).' '.$dimensions[0].'w';
        }

        ksort($candidates);

        return implode(', ', $candidates);
    }

    private static function preferredPath(string $path): string
    {
        $webpPath = preg_replace('/\.(?:jpe?g|png)$/i', '.webp', $path);

        return $webpPath !== $path && self::exists($webpPath) ? $webpPath : $path;
    }

    private static function assetUrl(string $path): string
    {
        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        return asset('storage/'.$path);
    }

    private static function exists(string $path): bool
    {
        if (str_starts_with($path, 'assets/')) {
            return is_file(public_path($path));
        }

        return Storage::disk('public')->exists($path);
    }

    private static function dimensions(string $path): ?array
    {
        if (str_starts_with($path, 'assets/')) {
            $absolutePath = public_path($path);
        } else {
            $absolutePath = Storage::disk('public')->path($path);
        }

        if (! is_file($absolutePath)) {
            return null;
        }

        $dimensions = @getimagesize($absolutePath);

        return $dimensions ? [(int) $dimensions[0], (int) $dimensions[1]] : null;
    }
}
