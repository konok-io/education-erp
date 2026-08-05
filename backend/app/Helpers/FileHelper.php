<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    /**
     * Upload a file.
     */
    public static function upload(
        UploadedFile $file,
        string $directory,
        ?string $filename = null
    ): string {
        $filename = $filename ?? self::generateFilename($file);
        $path = $file->storeAs($directory, $filename, 'public');
        
        return $path;
    }

    /**
     * Generate a unique filename.
     */
    public static function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return time() . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Delete a file.
     */
    public static function delete(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }

    /**
     * Move a file.
     */
    public static function move(string $source, string $destination): bool
    {
        if (Storage::disk('public')->exists($source)) {
            return Storage::disk('public')->move($source, $destination);
        }
        
        return false;
    }

    /**
     * Copy a file.
     */
    public static function copy(string $source, string $destination): bool
    {
        if (Storage::disk('public')->exists($source)) {
            return Storage::disk('public')->copy($source, $destination);
        }
        
        return false;
    }

    /**
     * Get file size in human readable format.
     */
    public static function getSize(string $path): string
    {
        if (Storage::disk('public')->exists($path)) {
            $bytes = Storage::disk('public')->size($path);
            return self::formatBytes($bytes);
        }
        
        return '0 B';
    }

    /**
     * Format bytes to human readable.
     */
    public static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file exists.
     */
    public static function exists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    /**
     * Get file URL.
     */
    public static function url(string $path): ?string
    {
        if (self::exists($path)) {
            return Storage::disk('public')->url($path);
        }
        
        return null;
    }

    /**
     * Get file extension.
     */
    public static function getExtension(UploadedFile $file): string
    {
        return strtolower($file->getClientOriginalExtension());
    }

    /**
     * Check if file is image.
     */
    public static function isImage(UploadedFile $file): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        return in_array(self::getExtension($file), $imageExtensions);
    }

    /**
     * Check if file is PDF.
     */
    public static function isPdf(UploadedFile $file): bool
    {
        return self::getExtension($file) === 'pdf';
    }

    /**
     * Get MIME type.
     */
    public static function getMimeType(UploadedFile $file): string
    {
        return $file->getMimeType();
    }
}
