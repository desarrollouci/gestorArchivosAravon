<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Image;
class Uploader {
	
    public static function uploadFile(string $key, string $path, string $user_id = null): string {
        $fileName = self::generateFileName($key, $user_id);
        request()->file($key)->storeAs($path, $fileName);
        return $fileName;
    }

    public static function removeFile(string $path, string $fileName) {
        Storage::delete(sprintf('%s/%s', $path, $fileName));
    }

    protected static function generateFileName(string $key, string $user_id = null): string {
        $extension = request()->file($key)->getClientOriginalExtension();
        return sprintf('%s-%s.%s', $user_id == null ? auth()->id() : $user_id, now()->timestamp, $extension);
    }

}