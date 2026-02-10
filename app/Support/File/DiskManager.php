<?php

namespace App\Support\File;

use App\Constant\UploadConstant;
use App\Support\File\FileSanitizer;
use App\Support\File\ImageOptimizer;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiskManager
{
    protected string $disk;
    protected FileSanitizer $sanitizer;
    protected ImageOptimizer $optimizer;

    public function __construct()
    {
        $this->disk = UploadConstant::DISK;

        $this->sanitizer = new FileSanitizer(
            UploadConstant::BLACKLIST_EXTENSIONS
        );

        $this->optimizer = new ImageOptimizer(
            maxWidth: UploadConstant::IMAGE_MAX_WIDTH,
            quality: UploadConstant::WEBP_QUALITY,
            format: UploadConstant::IMAGE_TARGET_FORMAT
        );
    }

    /**
     * Hàm Upload chính
     */
    public function upload(
        UploadedFile $file,
        string $module = UploadConstant::DEFAULT_MODULE,
        bool $optimize = UploadConstant::OPTIMIZE_IMAGE
    ): string
    {
        $this->sanitizer->validate($file);

        $folder = $this->generatePath($module);

        if ($optimize && $this->isImage($file)) {
            return $this->uploadOptimizedImage($file, $folder);
        }

        return $this->uploadRawFile($file, $folder);
    }

    // --- INTERNAL HANDLERS ---

    protected function uploadOptimizedImage(UploadedFile $file, string $folder): string
    {
        $encodedContent = $this->optimizer->process($file);

        $fileName = (string) Str::uuid() . '.' . $this->optimizer->getExtension();
        $fullPath = $folder . '/' . $fileName;

        Storage::disk($this->disk)->put($fullPath, $encodedContent);

        return $fullPath;
    }

    protected function uploadRawFile(UploadedFile $file, string $folder): string
    {
        $fileName = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($folder, $fileName, ['disk' => $this->disk]);
    }

    // --- HELPERS ---

    protected function generatePath(string $module): string
    {
        return $module . '/' . Carbon::now()->format('Y-m-d');
    }

    protected function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    public function delete(?string $path): bool
    {
        if (!$path) return false;

        return Storage::disk($this->disk)->exists($path) && Storage::disk($this->disk)->delete($path);
    }

    /**
     * Delete many files safely.
     *
     * @param array<int, string|null> $paths
     */
    public function deleteMany(array $paths): void
    {
        foreach ($paths as $path) {
            if (! $this->isExists($path)) {
                Log::error('File not found: ' . $path);
                continue;
            }

            $this->delete($path);
        }
    }

    public function url(?string $path): ?string
    {
        if (!$path) return null;
        if (Str::startsWith($path, ['http://', 'https://'])) return $path;

        return Storage::disk($this->disk)->url($path);
    }

    public function isExists(?string $path): bool
    {
        if (!$path) return false;

        return Storage::disk($this->disk)->exists($path);
    }
}
