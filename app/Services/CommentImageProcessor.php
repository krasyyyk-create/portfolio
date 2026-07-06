<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class CommentImageProcessor
{
    public const MAX_WIDTH = 480;

    public const MAX_HEIGHT = 320;

    /**
     * @return array{path: string, width: int, height: int}
     */
    public function process(UploadedFile $file): array
    {
        $dimensions = $this->readDimensions($file);

        if ($dimensions === null) {
            throw new RuntimeException('Unable to read image dimensions.');
        }

        [$width, $height] = $dimensions;

        if ($width <= self::MAX_WIDTH && $height <= self::MAX_HEIGHT) {
            $path = $file->store('comment-images', 'public');

            return [
                'path' => $path,
                'width' => $width,
                'height' => $height,
            ];
        }

        if ($file->getMimeType() === 'image/gif') {
            $path = $file->store('comment-images', 'public');

            return [
                'path' => $path,
                'width' => $width,
                'height' => $height,
            ];
        }

        return $this->cropAndStore($file, $width, $height);
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private function readDimensions(UploadedFile $file): ?array
    {
        $size = @getimagesize($file->getRealPath());

        if ($size === false) {
            return null;
        }

        return [(int) $size[0], (int) $size[1]];
    }

    /**
     * @return array{path: string, width: int, height: int}
     */
    private function cropAndStore(UploadedFile $file, int $width, int $height): array
    {
        $source = $this->createImageResource($file);

        if ($source === null) {
            throw new RuntimeException('Unable to process image.');
        }

        $targetRatio = self::MAX_WIDTH / self::MAX_HEIGHT;
        $sourceRatio = $width / $height;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $height;
            $cropWidth = (int) round($height * $targetRatio);
            $srcX = (int) round(($width - $cropWidth) / 2);
            $srcY = 0;
        } else {
            $cropWidth = $width;
            $cropHeight = (int) round($width / $targetRatio);
            $srcX = 0;
            $srcY = (int) round(($height - $cropHeight) / 2);
        }

        $destination = imagecreatetruecolor(self::MAX_WIDTH, self::MAX_HEIGHT);

        if ($destination === false) {
            imagedestroy($source);

            throw new RuntimeException('Unable to create image canvas.');
        }

        $this->preserveTransparency($destination, $file->getMimeType());

        imagecopyresampled(
            $destination,
            $source,
            0,
            0,
            $srcX,
            $srcY,
            self::MAX_WIDTH,
            self::MAX_HEIGHT,
            $cropWidth,
            $cropHeight
        );

        imagedestroy($source);

        $extension = $this->resolveOutputExtension($file);
        $directory = 'comment-images';
        Storage::disk('public')->makeDirectory($directory);
        $filename = Str::uuid()->toString().'.'.$extension;
        $path = $directory.'/'.$filename;
        $fullPath = Storage::disk('public')->path($path);

        if (! $this->writeImage($destination, $fullPath, $file->getMimeType())) {
            imagedestroy($destination);

            throw new RuntimeException('Unable to save processed image.');
        }

        imagedestroy($destination);

        return [
            'path' => $path,
            'width' => self::MAX_WIDTH,
            'height' => self::MAX_HEIGHT,
        ];
    }

    private function createImageResource(UploadedFile $file): ?\GdImage
    {
        $path = $file->getRealPath();

        return match ($file->getMimeType()) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path) ?: null,
            'image/png' => @imagecreatefrompng($path) ?: null,
            'image/webp' => function_exists('imagecreatefromwebp') ? (@imagecreatefromwebp($path) ?: null) : null,
            default => null,
        };
    }

    private function preserveTransparency(\GdImage $image, string $mimeType): void
    {
        if (! in_array($mimeType, ['image/png', 'image/webp', 'image/gif'], true)) {
            return;
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefilledrectangle($image, 0, 0, self::MAX_WIDTH, self::MAX_HEIGHT, $transparent);
    }

    private function resolveOutputExtension(UploadedFile $file): string
    {
        return match ($file->getMimeType()) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }

    private function writeImage(\GdImage $image, string $path, string $mimeType): bool
    {
        return match ($mimeType) {
            'image/png' => imagepng($image, $path),
            'image/webp' => function_exists('imagewebp') ? imagewebp($image, $path, 85) : false,
            default => imagejpeg($image, $path, 85),
        };
    }
}
