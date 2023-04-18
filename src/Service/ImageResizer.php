<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageResizer
{
    public function resize(string $inputPath, string $outputPath, int $width, int $height): void
    {
        $imagine = new Imagine();
        $image = $imagine->open($inputPath);

        $size = new Box($width, $height);
        $resizedImage = $image->thumbnail($size, 'inset');
        $resizedImage->save($outputPath);
    }
}
