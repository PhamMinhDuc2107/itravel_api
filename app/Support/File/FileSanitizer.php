<?php

namespace App\Support\File;

use Exception;

use Illuminate\Http\UploadedFile;
class FileSanitizer
{
    public function __construct(
        protected array $blacklist
    ) {}

    public function validate(UploadedFile $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (in_array($ext, $this->blacklist)) {
            throw new Exception("File type '{$ext}' is strictly prohibited.");
        }

        $originalName = $file->getClientOriginalName();
        if (preg_match('/\.(php|phtml|exe|sh|bat)\./i', $originalName)) {
            throw new Exception("Malicious filename pattern detected!");
        }

        $validImages = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $mime = $file->getMimeType();

        if (in_array($ext, $validImages) && !str_starts_with($mime, 'image/')) {
            throw new Exception("File content does not match image extension.");
        }
    }
}
