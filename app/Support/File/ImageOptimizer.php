<?php

namespace App\Support\File;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;
class ImageOptimizer
{
    public function __construct(
        protected int $maxWidth,
        protected int $quality,
        protected string $format
    ) {}

    public function process(UploadedFile $file): string
    {
        $image = Image::read($file);

        $image->scaleDown(width: $this->maxWidth);

        $method = 'to' . ucfirst($this->format);

        if (method_exists($image, $method)) {
            return (string) $image->$method(quality: $this->quality);
        }

        return (string) $image->toWebp(quality: $this->quality);
    }

    public function getExtension(): string
    {
        return $this->format;
    }
}
