<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadOptimizer
{
    public static function store(UploadedFile $file, string $directory, int $maxDimension = 1600): string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return $file->store($directory, 'public');
        }

        $sourceData = @file_get_contents($file->getRealPath());
        $source = $sourceData ? @imagecreatefromstring($sourceData) : false;

        if (! $source) {
            return $file->store($directory, 'public');
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = min(1, $maxDimension / max($sourceWidth, $sourceHeight));
        $width = max(1, (int) round($sourceWidth * $scale));
        $height = max(1, (int) round($sourceHeight * $scale));
        $target = imagecreatetruecolor($width, $height);

        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $width, $height, $transparent);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        ob_start();
        $encoded = imagewebp($target, null, 82);
        $contents = ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        if (! $encoded || ! $contents) {
            return $file->store($directory, 'public');
        }

        $filename = pathinfo($file->hashName(), PATHINFO_FILENAME).'.webp';
        $path = trim($directory, '/').'/'.$filename;
        Storage::disk('public')->put($path, $contents);

        return $path;
    }
}
