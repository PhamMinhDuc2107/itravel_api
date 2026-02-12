<?php

namespace App\Service\Admin;

use App\Constant\UploadConstant;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\BannerRepositoryInterface;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class BannerService
{
    public function __construct(
        private BannerRepositoryInterface $bannerRepository,
        private DiskManager $diskManager,
    ) {
    }

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->bannerRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $banner = $this->bannerRepository->find($id);

        if (!$banner) {
            throw new NotFoundException('Banner', $id);
        }

        return $banner;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $uploadedImagePath = null;
            $uploadedMobileImagePath = null;

            try {
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    $uploadedImagePath = $this->uploadImage($data['image']);
                    $data['image'] = $uploadedImagePath;
                }

                if (isset($data['mobile_image']) && $data['mobile_image'] instanceof UploadedFile) {
                    $uploadedMobileImagePath = $this->uploadImage($data['mobile_image']);
                    $data['mobile_image'] = $uploadedMobileImagePath;
                }

                return $this->bannerRepository->create($data);

            } catch (\Exception $e) {
                if ($uploadedImagePath) {
                    $this->diskManager->delete($uploadedImagePath);
                }
                if ($uploadedMobileImagePath) {
                    $this->diskManager->delete($uploadedMobileImagePath);
                }

                throw $e;
            }
        });
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, $data): Model
    {
        $banner = $this->bannerRepository->find($id);

        if (!$banner) {
            throw new NotFoundException('Banner', $id);
        }

        return DB::transaction(function () use ($id, $banner, $data) {
            $uploadedImagePath = null;
            $uploadedMobileImagePath = null;
            $oldImage = $banner->image;
            $oldMobileImage = $banner->mobile_image;

            try {
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    if ($oldImage) {
                        $this->diskManager->delete($oldImage);
                    }

                    $uploadedImagePath = $this->uploadImage($data['image']);
                    $data['image'] = $uploadedImagePath;
                }

                if (isset($data['mobile_image']) && $data['mobile_image'] instanceof UploadedFile) {
                    if ($oldMobileImage) {
                        $this->diskManager->delete($oldMobileImage);
                    }

                    $uploadedMobileImagePath = $this->uploadImage($data['mobile_image']);
                    $data['mobile_image'] = $uploadedMobileImagePath;
                }

                return $this->bannerRepository->update($id, $data);

            } catch (\Exception $e) {
                if ($uploadedImagePath) {
                    $this->diskManager->delete($uploadedImagePath);
                }
                if ($uploadedMobileImagePath) {
                    $this->diskManager->delete($uploadedMobileImagePath);
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
        $banner = $this->bannerRepository->find($id);

        if (!$banner) {
            throw new NotFoundException('Banner', $id);
        }

        return DB::transaction(function () use ($id, $banner) {
            $deleted = $this->bannerRepository->delete($id);

            if ($deleted) {
                if ($banner->image) {
                    $this->diskManager->delete($banner->image);
                }
                if ($banner->mobile_image) {
                    $this->diskManager->delete($banner->mobile_image);
                }
            }

            return $deleted;
        });
    }

    public function destroyMultiple(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $banners = $this->bannerRepository->findAllBy(['id' => $ids]);
            $deleted = $this->bannerRepository->deleteMultiple($ids);

            if ($deleted) {
                foreach ($banners as $banner) {
                    if ($banner->image) {
                        $this->diskManager->delete($banner->image);
                    }
                    if ($banner->mobile_image) {
                        $this->diskManager->delete($banner->mobile_image);
                    }
                }
            }

            return $deleted;
        });
    }

    private function uploadImage(UploadedFile $file): string
    {
        $module = UploadConstant::BANNER_MODULE;
        return $this->diskManager->upload($file, $module);
    }
}

