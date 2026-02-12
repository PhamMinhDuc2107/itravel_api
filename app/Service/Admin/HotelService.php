<?php

namespace App\Service\Admin;

use App\Constant\UploadConstant;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\HotelRepositoryInterface;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class HotelService
{
    public function __construct(
        private HotelRepositoryInterface $hotelRepository,
        private DiskManager $diskManager,
    ) {
    }

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->hotelRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $hotel = $this->hotelRepository->find($id, ['location', 'hotelType', 'amenities', 'reviews']);

        if (!$hotel) {
            throw new NotFoundException('Hotel', $id);
        }

        return $hotel;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $amenityIds = $data['amenity_ids'] ?? null;
            unset($data['amenity_ids']);

            [$dataWithFiles, $newFiles] = $this->prepareImages($data);

            try {
                /** @var Model $hotel */
                $hotel = $this->hotelRepository->create($dataWithFiles);

                if (is_array($amenityIds)) {
                    $hotel->amenities()->sync($amenityIds);
                }

                return $hotel;
            } catch (\Throwable $e) {
                $this->diskManager->deleteMany($newFiles);
                throw $e;
            }
        });
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $hotel = $this->hotelRepository->find($id);

        if (!$hotel) {
            throw new NotFoundException('Hotel', $id);
        }

        return DB::transaction(function () use ($id, $data, $hotel) {
            $amenityIds = $data['amenity_ids'] ?? null;
            unset($data['amenity_ids']);

            $oldImage = $hotel->image;
            $oldGallery = is_array($hotel->gallery) ? $hotel->gallery : [];

            [$dataWithFiles, $newFiles] = $this->prepareImages($data);

            try {
                /** @var Model $updatedHotel */
                $updatedHotel = $this->hotelRepository->update($id, $dataWithFiles);

                if (is_array($amenityIds)) {
                    $hotel->amenities()->sync($amenityIds);
                }

                $this->diskManager->deleteMany([$oldImage, ...$oldGallery]);

                return $updatedHotel;
            } catch (\Throwable $e) {
                $this->diskManager->deleteMany($newFiles);
                throw $e;
            }
        });
    }

    /**
     * @throws NotFoundException
     */
    public function destroy(int $id): bool
    {
        $hotel = $this->hotelRepository->find($id);

        if (!$hotel) {
            throw new NotFoundException('Hotel', $id);
        }

        return DB::transaction(function () use ($id, $hotel) {
            $hotel->amenities()->detach();

            $deleted = $this->hotelRepository->delete($id);

            if ($deleted) {
                $files = [];
                if ($hotel->image) {
                    $files[] = $hotel->image;
                }
                if (is_array($hotel->gallery)) {
                    $files = array_merge($files, $hotel->gallery);
                }

                $this->diskManager->deleteMany($files);
            }

            return $deleted;
        });
    }

    public function destroyMultiple(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $hotels = $this->hotelRepository->findAllBy(['id' => $ids]);

            foreach ($hotels as $hotel) {
                $hotel->amenities()->detach();
            }

            $deleted = $this->hotelRepository->deleteMultiple($ids);

            if ($deleted) {
                $files = [];
                foreach ($hotels as $hotel) {
                    if ($hotel->image) {
                        $files[] = $hotel->image;
                    }
                    if (is_array($hotel->gallery)) {
                        $files = array_merge($files, $hotel->gallery);
                    }
                }
                $this->diskManager->deleteMany($files);
            }

            return $deleted;
        });
    }

    /**
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
        return $this->diskManager->upload($file, UploadConstant::HOTEL_MODULE);
    }
}


