<?php

namespace App\Service\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\AmenityRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use App\Constant\UploadConstant;
use App\Support\File\DiskManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class AmenityService
{
    public function __construct(
        private AmenityRepositoryInterface $amenityRepository,
        private DiskManager $diskManager,
    ) {}

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->amenityRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $amenity = $this->amenityRepository->find($id);

        if (! $amenity) {
            throw new NotFoundException('Amenity', $id);
        }

        return $amenity;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $uploadedFiles = [];

            try {
                if (isset($data['icon']) && $data['icon'] instanceof UploadedFile) {
                    $path = $this->uploadIcon($data['icon']);
                    $uploadedFiles[] = $path;
                    $data['icon'] = $path;
                }

                return $this->amenityRepository->create($data);
            } catch (\Throwable $e) {
                $this->diskManager->deleteMany($uploadedFiles);
                throw $e;
            }
        });
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $amenity = $this->amenityRepository->find($id);

        if (! $amenity) {
            throw new NotFoundException('Amenity', $id);
        }

        return DB::transaction(function () use ($id, $data, $amenity) {
            $oldIcon = $amenity->icon;
            $uploadedFiles = [];

            try {
                if (isset($data['icon']) && $data['icon'] instanceof UploadedFile) {
                    $path = $this->uploadIcon($data['icon']);
                    $uploadedFiles[] = $path;
                    $data['icon'] = $path;
                }

                /** @var Model $updated */
                $updated = $this->amenityRepository->update($id, $data);

                if ($oldIcon) {
                    $this->diskManager->deleteMany([$oldIcon]);
                }

                return $updated;
            } catch (\Throwable $e) {
                $this->diskManager->deleteMany($uploadedFiles);
                throw $e;
            }
        });
    }

    /**
     * @throws NotFoundException
     */
    public function destroy(int $id): bool
    {
        $amenity = $this->amenityRepository->find($id);

        if (! $amenity) {
            throw new NotFoundException('Amenity', $id);
        }

        return DB::transaction(function () use ($id, $amenity) {
            $deleted = $this->amenityRepository->delete($id);

            if ($deleted && $amenity->icon) {
                $this->diskManager->deleteMany([$amenity->icon]);
            }

            return $deleted;
        });
    }

    private function uploadIcon(UploadedFile $file): string
    {
        return $this->diskManager->upload($file, UploadConstant::AMENITY_MODULE);
    }
}


