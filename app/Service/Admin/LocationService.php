<?php

namespace App\Service\Admin;

use App\Constant\UploadConstant;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\LocationRepositoryInterface;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class LocationService
{
    public function __construct(
        private LocationRepositoryInterface $locationRepository,
        private DiskManager $diskManager,
    ) {}

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->locationRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $location = $this->locationRepository->find($id);

        if (! $location) {
            throw new NotFoundException('Location', $id);
        }

        return $location;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $uploadedImagePath = null;

            try {
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    $uploadedImagePath = $this->uploadImage($data['image']);
                    $data['image'] = $uploadedImagePath;
                }

                return $this->locationRepository->create($data);

            } catch (\Exception $e) {
                if ($uploadedImagePath) {
                    $this->diskManager->delete($uploadedImagePath);
                }

                throw $e;
            }
        });
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $location = $this->locationRepository->find($id);

        if (!$location) {
            throw new NotFoundException('Location', $id);
        }

        return DB::transaction(function () use ($id, $location, $data) {
            $uploadedImagePath = null;
            $oldImage = $location->image;

            try {
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    if ($oldImage) {
                        $this->diskManager->delete($oldImage);
                    }

                    $uploadedImagePath = $this->uploadImage($data['image']);
                    $data['image'] = $uploadedImagePath;
                }

                return $this->locationRepository->update($id, $data);

            } catch (\Exception $e) {
                if ($uploadedImagePath) {
                    $this->diskManager->delete($uploadedImagePath);
                }

                throw $e;
            }
        });
    }

    /**
     * @throws NotFoundException
     */
    public function destroy(int $id): bool
    {
        $location = $this->locationRepository->find($id);

        if (!$location) {
            throw new NotFoundException('Location', $id);
        }

        return DB::transaction(function () use ($id, $location) {
            $deleted = $this->locationRepository->delete($id);

            if ($deleted && $location->image) {
                $this->diskManager->delete($location->image);
            }

            return $deleted;
        });
    }

    private function uploadImage(UploadedFile $file): string
    {
        $module = UploadConstant::DEFAULT_MODULE;
        return $this->diskManager->upload($file, $module);
    }
}

