<?php

namespace App\Service\Admin;

use App\Constant\UploadConstant;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Jobs\ImportToursJob;
use Illuminate\Support\Facades\Log;
use App\Repository\Contract\TourRepositoryInterface;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

readonly class TourService
{
    public function __construct(
        private TourRepositoryInterface $tourRepository,
        private DiskManager $diskManager,
    ) {
    }

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->tourRepository->list($context, $searchFields);
    }

    public function show(int $id): Model
    {
        $tour = $this->tourRepository->find($id, ['category', 'departureLocation', 'destinationLocation']);

        if (!$tour) {
            throw new NotFoundException('Tour', $id);
        }

        return $tour;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            [$dataWithFiles, $newFiles] = $this->prepareImages($data);

            try {
                return $this->tourRepository->create($dataWithFiles);
            } catch (\Throwable $e) {
                $this->diskManager->deleteMany($newFiles);
                throw $e;
            }
        });
    }

    public function update(int $id, array $data): Model
    {
        $tour = $this->tourRepository->find($id);

        if (!$tour) {
            throw new NotFoundException('Tour', $id);
        }

        return DB::transaction(function () use ($id, $data, $tour) {
            $oldImage = $tour->image;
            $oldGallery = is_array($tour->gallery) ? $tour->gallery : [];

            [$dataWithFiles, $newFiles] = $this->prepareImages($data);

            try {
                /** @var Model $updated */
                $updated = $this->tourRepository->update($id, $dataWithFiles);

                // Sau khi update thành công mới xoá file cũ
                $this->diskManager->deleteMany([$oldImage, ...$oldGallery]);

                return $updated;
            } catch (\Throwable $e) {
                $this->diskManager->deleteMany($newFiles);
                throw $e;
            }
        });
    }

    public function destroy(int $id): bool
    {
        $tour = $this->tourRepository->find($id);

        if (!$tour) {
            throw new NotFoundException('Tour', $id);
        }

        return DB::transaction(function () use ($id, $tour) {
            $deleted = $this->tourRepository->delete($id);

            if ($deleted) {
                $files = [];
                if ($tour->image) {
                    $files[] = $tour->image;
                }
                if (is_array($tour->gallery)) {
                    $files = array_merge($files, $tour->gallery);
                }

                $this->diskManager->deleteMany($files);
            }

            return $deleted;
        });
    }

    public function destroyMultiple(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $tours = $this->tourRepository->findAllBy(['id' => $ids]);
            $deleted = $this->tourRepository->deleteMultiple($ids);

            if ($deleted) {
                $files = [];
                foreach ($tours as $tour) {
                    if ($tour->image) {
                        $files[] = $tour->image;
                    }
                    if (is_array($tour->gallery)) {
                        $files = array_merge($files, $tour->gallery);
                    }
                }
                $this->diskManager->deleteMany($files);
            }

            return $deleted;
        });
    }

    /**
     * Import tours from an uploaded Excel file via background job.
     *
     * @return array{message: string, file: string}
     */
    public function import(UploadedFile $file): array
    {
        Log::channel('daily')->info('[TourImport] Request received', [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ]);

        $fileName = 'tour_import_' . now()->format('YmdHis') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        try {
            $storedPath = $file->storeAs('imports/tours', $fileName, 'local');

            Log::channel('daily')->info('[TourImport] File stored', [
                'stored_path' => $storedPath,
                'file_name' => $fileName,
            ]);

            ImportToursJob::dispatch($storedPath);

            Log::channel('daily')->info('[TourImport] Job dispatched', [
                'stored_path' => $storedPath,
                'queue_driver' => config('queue.default'),
            ]);
        } catch (\Throwable $e) {
            Log::channel('daily')->error('[TourImport] Service error', [
                'file_name' => $fileName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }

        return [
            'message' => 'Import queued for background processing',
            'file' => $fileName,
        ];
    }

    /**
     * Generate the Excel import template and return its file path.
     */
    public function generateTemplate(): string
    {
        $outputPath = storage_path('app/templates/tour_import_template_' . uniqid() . '.xlsx');

        Artisan::call('tour:generate-template', ['--output' => $outputPath]);

        return $outputPath;
    }

    /**
     * Upload image(s) for main image and gallery.
     *
     * @return array{0: array, 1: string[]} [dataWithFiles, uploadedFiles]
     */
    private function prepareImages(array $data): array
    {
        $uploadedFiles = [];

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $path = $this->uploadImage($data['image']);
            $uploadedFiles[] = $path;
            $data['image'] = $path;
        }

        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $galleryPaths = [];

            foreach ($data['gallery'] as $file) {
                if ($file instanceof UploadedFile) {
                    $path = $this->uploadImage($file);
                    $galleryPaths[] = $path;
                    $uploadedFiles[] = $path;
                }
            }

            $data['gallery'] = $galleryPaths;
        }

        return [$data, $uploadedFiles];
    }

    private function uploadImage(UploadedFile $file): string
    {
        $module = UploadConstant::TOUR_MODULE;

        return $this->diskManager->upload($file, $module);
    }
}


