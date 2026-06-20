<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function upload(UploadedFile $file, string $context, int $id, string $disk = 'public'): string
    {
        $timestamp = time();
        $ext       = $file->getClientOriginalExtension();
        $filename  = "{$context}_{$id}_{$timestamp}.{$ext}";
        $folder    = self::getFolderByContext($context);

        $file->storeAs($folder, $filename, $disk);

        return "storage/{$folder}/{$filename}";
    }

    public static function delete(?string $path, string $disk = 'public'): void
    {
        if (!$path) return;

        $storagePath = str_replace('storage/', '', $path);
        if (Storage::disk($disk)->exists($storagePath)) {
            Storage::disk($disk)->delete($storagePath);
        }
    }

    protected static function getFolderByContext(string $context): string
    {
        return match($context) {
            'school_logo'     => 'schools/logos',
            'school_banner'   => 'schools/banners',
            'student_photo'   => 'students/photos',
            'teacher_photo'   => 'teachers/photos',
            'gallery_item'    => 'schools/galleries',
            'post_thumbnail'  => 'posts/thumbnails',
            'document'        => 'documents',
            'activity_media'  => 'activities/media',
            default           => 'others',
        };
    }
}
