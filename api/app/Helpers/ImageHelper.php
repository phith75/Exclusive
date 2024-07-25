<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @param int|null $width
     * @param int|null $height
     * @param string|null $oldFilename
     *
     * @return string|null
     */
    public static function uploadAndResize($file, $path, $width = null, $height = null, $oldFilename = null)
    {
        if (! $file->isValid()) {
            return null;
        }

        // Xóa ảnh cũ nếu có
        if ($oldFilename) {
            self::delete($path, $oldFilename);
        }

        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $image = imagecreatefromstring(file_get_contents($file));

        if ($width && $height) {
            $resizedImage = imagescale($image, $width, $height);
        } elseif ($width) {
            $resizedImage = imagescale($image, $width);
        } elseif ($height) {
            $aspectRatio = imagesx($image) / imagesy($image);
            $resizedImage = imagescale($image, $height * $aspectRatio, $height);
        } else {
            $resizedImage = $image;
        }

        ob_start();
        imagejpeg($resizedImage);
        $imageData = ob_get_clean();

        imagedestroy($image);
        imagedestroy($resizedImage);

        Storage::put($path . '/' . $filename, $imageData);

        return $filename;
    }

    /**
     * @param string $path
     * @param string $filename
     *
     * @return bool
     */
    public static function delete($path, $filename)
    {
        return Storage::delete($path . '/' . $filename);
    }
}
