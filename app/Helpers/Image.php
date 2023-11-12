<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image
{

    /**
     * @param string $base_64_string
     *
     * @return false|string
     */
    public static function makeImageFromBase(string $base_64_string)
    {
        $image_parts = explode(";base64,", $base_64_string);

        return base64_decode($image_parts[1]);
    }


    /**
     * @param string $folder
     * @param string $path
     *
     * @return string
     */
    public static function cleanPath(string $disk, string $folder, string $path): string
    {
        return str_replace(config('filesystems.disks.' . $disk . '.url') . $folder . '/', '', $path);
    }


    /**
     * @param        $image
     * @param string $target
     *
     * @return int
     */
    public static function setPreferedWidth($image, string $target = 'image'): array
    {
        $ratio = explode('x', config('settings.' . $target . '_size_ratio'));

        $width  = $ratio[0];
        $height = $ratio[1];

        if ($image->getWidth() < $image->getHeight()) {
            $width  = $ratio[1];
            $height = $ratio[0];
        }

        return [
            'width'  => $width,
            'height' => $height
        ];
    }


    /**
     * @param string $disk
     * @param string $new_image
     * @param        $resource
     *
     * @return string
     */
    public static function save(string $disk, array $new_image, $resource): string
    {
        $time  = Str::random(4);
        $image = json_decode($new_image['image']);
        $img   = \Intervention\Image\Facades\Image::make(self::makeImageFromBase($image->output->image));

        // Image creation
        $img_ratio = static::setPreferedWidth($img);
        $path      = $resource->id . '/' . Str::slug($resource->title) . '-' . $time . '.';

        $img = $img->resize($img_ratio['width'], null, function ($constraint) {
            $constraint->aspectRatio();
        })->resizeCanvas($img_ratio['width'], $img_ratio['height']);

        $path_jpg  = $path . 'jpg';
        $path_webp = $path . 'webp';

        Storage::disk($disk)->put($path_jpg, $img->encode('jpg'));
        Storage::disk($disk)->put($path_webp, $img->encode('webp'));

        // Thumb creation
        $thumb_ratio = static::setPreferedWidth($img, 'thumb');
        $path_thumb  = $resource->id . '/' . Str::slug($resource->title) . '-' . $time . '-thumb.';

        $img = $img->resize($thumb_ratio['width'], null, function ($constraint) {
            $constraint->aspectRatio();
        })->resizeCanvas($thumb_ratio['width'], $thumb_ratio['height']);

        $path_webp_thumb = $path_thumb . 'webp';
        Storage::disk($disk)->put($path_webp_thumb, $img->encode('webp'));

        return $path_jpg;
    }


    /**
     * @param string $disk
     * @param string $folder
     * @param string $path
     */
    public static function delete(string $disk, string $folder, string $path): void
    {
        $webp  = str_replace('.jpg', '.webp', $path);
        $thumb = str_replace('.jpg', '-thumb.webp', $path);

        Storage::disk($disk)->delete($folder . '/' . $path);
        Storage::disk($disk)->delete($folder . '/' . $webp);
        Storage::disk($disk)->delete($folder . '/' . $thumb);
    }

}
