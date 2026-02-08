<?php

namespace App\Service\Admin;

use App\Constant\UploadConstant;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\BlogRepositoryInterface;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class BlogService
{
    public function __construct(
        private BlogRepositoryInterface $blogRepository,
        private DiskManager $diskManager,
    ) {}

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->blogRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $blog = $this->blogRepository->find($id);

        if (! $blog) {
            throw new NotFoundException('Blog', $id);
        }

        return $blog;
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

                return $this->blogRepository->create($data);

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
        $blog = $this->blogRepository->find($id);

        if (!$blog) {
            throw new NotFoundException('Blog', $id);
        }

        return DB::transaction(function () use ($id, $blog, $data) {
            $uploadedImagePath = null;
            $oldImage = $blog->image;

            try {
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    if ($oldImage) {
                        $this->diskManager->delete($oldImage);
                    }

                    $uploadedImagePath = $this->uploadImage($data['image']);
                    $data['image'] = $uploadedImagePath;
                }

                return $this->blogRepository->update($id, $data);

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
        $blog = $this->blogRepository->find($id);

        if (!$blog) {
            throw new NotFoundException('Blog', $id);
        }

        return DB::transaction(function () use ($id, $blog) {
            $deleted = $this->blogRepository->delete($id);

            if ($deleted && $blog->image) {
                $this->diskManager->delete($blog->image);
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

