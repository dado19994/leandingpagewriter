<?php

namespace App\Actions;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StorePublicImage
{
    private const int MaxWidth = 1600;

    private const int WebpQuality = 82;

    public function handle(UploadedFile $file, string $field): string
    {
        $image = $this->decode($file, $field);
        $directory = public_path('images/uploads');
        $temporaryPath = null;

        try {
            if (! File::isDirectory($directory) && ! File::makeDirectory($directory, 0755, true)) {
                throw ValidationException::withMessages([
                    $field => 'Non è stato possibile preparare la cartella di destinazione.',
                ]);
            }

            $image = $this->resizeIfRequired($image, $field);
            $temporaryPath = tempnam($directory, 'writer-upload-');

            if ($temporaryPath === false || ! imagewebp($image, $temporaryPath, self::WebpQuality)) {
                throw ValidationException::withMessages([
                    $field => 'Non è stato possibile convertire l’immagine in WebP.',
                ]);
            }

            clearstatcache(true, $temporaryPath);

            if (! File::exists($temporaryPath) || File::size($temporaryPath) === 0) {
                throw ValidationException::withMessages([
                    $field => 'La conversione dell’immagine ha prodotto un file non valido.',
                ]);
            }

            $filename = Str::random(40).'.webp';
            $target = $directory.DIRECTORY_SEPARATOR.$filename;

            if (! File::move($temporaryPath, $target)) {
                throw ValidationException::withMessages([
                    $field => 'Non è stato possibile salvare l’immagine.',
                ]);
            }

            $temporaryPath = null;

            return 'images/uploads/'.$filename;
        } finally {
            imagedestroy($image);

            if ($temporaryPath !== null && File::exists($temporaryPath)) {
                File::delete($temporaryPath);
            }
        }
    }

    private function decode(UploadedFile $file, string $field): GdImage
    {
        $path = $file->getRealPath();

        try {
            $image = match ($file->getMimeType()) {
                'image/jpeg' => @imagecreatefromjpeg($path),
                'image/png' => @imagecreatefrompng($path),
                'image/webp' => @imagecreatefromwebp($path),
                default => false,
            };
        } catch (\Throwable) {
            $image = false;
        }

        if (! $image instanceof GdImage) {
            throw ValidationException::withMessages([
                $field => 'L’immagine non può essere decodificata. Usa un file JPEG, PNG o WebP valido.',
            ]);
        }

        return $image;
    }

    private function resizeIfRequired(GdImage $image, string $field): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= self::MaxWidth) {
            return $image;
        }

        $newWidth = self::MaxWidth;
        $newHeight = (int) round($height * ($newWidth / $width));
        $resized = imagecreatetruecolor($newWidth, $newHeight);

        if (! $resized instanceof GdImage) {
            throw ValidationException::withMessages([
                $field => 'Non è stato possibile ridimensionare l’immagine.',
            ]);
        }

        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        if (! imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
            imagedestroy($resized);

            throw ValidationException::withMessages([
                $field => 'Non è stato possibile ridimensionare l’immagine.',
            ]);
        }

        imagedestroy($image);

        return $resized;
    }
}
