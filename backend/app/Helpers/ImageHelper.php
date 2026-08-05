<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    /**
     * Upload and resize image.
     */
    public static function uploadAndResize(
        UploadedFile $file,
        string $directory,
        int $width = 800,
        int $height = 600,
        ?string $filename = null
    ): string {
        $filename = $filename ?? FileHelper::generateFilename($file);
        
        $image = Image::make($file->getRealPath());
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, $image->encode());
        
        return $path;
    }

    /**
     * Create thumbnail.
     */
    public static function createThumbnail(
        UploadedFile $file,
        string $directory,
        int $size = 150,
        ?string $filename = null
    ): string {
        $filename = $filename ?? 'thumb_' . FileHelper::generateFilename($file);
        
        $image = Image::make($file->getRealPath());
        $image->fit($size, $size);
        
        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, $image->encode());
        
        return $path;
    }

    /**
     * Compress image.
     */
    public static function compress(
        UploadedFile $file,
        string $directory,
        int $quality = 70,
        ?string $filename = null
    ): string {
        $filename = $filename ?? FileHelper::generateFilename($file);
        
        $image = Image::make($file->getRealPath());
        $image->encode('jpg', $quality);
        
        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, $image);
        
        return $path;
    }

    /**
     * Delete image.
     */
    public static function delete(string $path): bool
    {
        return FileHelper::delete($path);
    }

    /**
     * Get image URL.
     */
    public static function url(string $path): ?string
    {
        return FileHelper::url($path);
    }

    /**
     * Check if file is image.
     */
    public static function isImage(UploadedFile $file): bool
    {
        return FileHelper::isImage($file);
    }

    /**
     * Resize image to specific dimensions.
     */
    public static function resize(
        string $path,
        int $width,
        int $height
    ): bool {
        if (!Storage::disk('public')->exists($path)) {
            return false;
        }
        
        $content = Storage::disk('public')->get($path);
        $image = Image::make($content);
        $image->resize($width, $height);
        Storage::disk('public')->put($path, $image->encode());
        
        return true;
    }

    /**
     * Crop image.
     */
    public static function crop(
        string $path,
        int $width,
        int $height,
        int $x,
        int $y
    ): bool {
        if (!Storage::disk('public')->exists($path)) {
            return false;
        }
        
        $content = Storage::disk('public')->get($path);
        $image = Image::make($content);
        $image->crop($width, $height, $x, $y);
        Storage::disk('public')->put($path, $image->encode());
        
        return true;
    }

    /**
     * Add watermark to image.
     */
    public static function addWatermark(
        string $path,
        string $watermarkPath,
        string $position = 'bottom-right',
        int $opacity = 50
    ): bool {
        if (!Storage::disk('public')->exists($path)) {
            return false;
        }
        
        $content = Storage::disk('public')->get($path);
        $image = Image::make($content);
        
        $watermark = Image::make($watermarkPath);
        $watermark->opacity($opacity);
        
        switch ($position) {
            case 'top-left':
                $image->insert($watermark, 'top-left', 10, 10);
                break;
            case 'top-right':
                $image->insert($watermark, 'top-right', 10, 10);
                break;
            case 'bottom-left':
                $image->insert($watermark, 'bottom-left', 10, 10);
                break;
            case 'bottom-right':
            default:
                $image->insert($watermark, 'bottom-right', 10, 10);
                break;
            case 'center':
                $image->insert($watermark, 'center');
                break;
        }
        
        Storage::disk('public')->put($path, $image->encode());
        
        return true;
    }
}
