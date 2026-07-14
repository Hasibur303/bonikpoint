<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class OptimizedImage
{
    public static function url(string $path): string
    {
        $webpPath = preg_replace('/\.(?:jpe?g|png)$/i', '.webp', $path);

        if (str_starts_with($path, 'assets/')) {
            if ($webpPath !== $path && is_file(public_path($webpPath))) {
                return asset($webpPath);
            }

            return asset($path);
        }

        if ($webpPath !== $path && Storage::disk('public')->exists($webpPath)) {
            return asset('storage/'.$webpPath);
        }

        return asset('storage/'.$path);
    }
}
