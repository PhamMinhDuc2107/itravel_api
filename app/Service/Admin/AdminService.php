<?php

namespace App\Service\Admin;

use App\Constant\UploadConstant;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\AdminRepositoryInterface;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

readonly class AdminService
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private DiskManager $diskManager,
    ) {
    }

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->adminRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $admin = $this->adminRepository->find($id);

        if (!$admin) {
            throw new NotFoundException('Admins', $id);
        }

        return $admin;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $uploadedPath = null;

            try {

                if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                    $uploadedPath = $this->uploadImageAvatar($data['avatar']);
                    ;
                    $data['avatar'] = $uploadedPath;
                }

                if (!empty($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                }

                return $this->adminRepository->create($data);

            } catch (\Exception $e) {
                if ($uploadedPath) {
                    $this->diskManager->delete($uploadedPath);
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
        $admin = $this->adminRepository->find($id);

        if (!$admin) {
            throw new NotFoundException('Admin', $id);
        }

        return DB::transaction(function () use ($id, $admin, $data) {
            $uploadedPath = null;
            $oldAvatar = $admin->avatar;

            try {
                if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {

                    if ($oldAvatar) {
                        $this->diskManager->delete($oldAvatar);
                    }

                    $uploadedPath = $this->uploadImageAvatar($data['avatar']);
                    $data['avatar'] = $uploadedPath;
                }

                if (!empty($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                } else {
                    unset($data['password']);
                }

                return $this->adminRepository->update($id, $data);

            } catch (\Exception $e) {

                if ($uploadedPath) {
                    $this->diskManager->delete($uploadedPath);
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
        $admin = $this->adminRepository->find($id);

        if (!$admin) {
            throw new NotFoundException('Admin', $id);
        }

        return DB::transaction(function () use ($id, $admin) {
            $deleted = $this->adminRepository->delete($id);

            if ($deleted && $admin->avatar) {
                $this->diskManager->delete($admin->avatar);
            }

            return $deleted;
        });
    }

    public function destroyMultiple(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $admins = $this->adminRepository->findAllBy(['id' => $ids]);
            $deleted = $this->adminRepository->deleteMultiple($ids);

            if ($deleted) {
                foreach ($admins as $admin) {
                    if ($admin->avatar) {
                        $this->diskManager->delete($admin->avatar);
                    }
                }
            }

            return $deleted;
        });
    }

    private function uploadImageAvatar(UploadedFile $file): string
    {
        $module = UploadConstant::AVATAR_MODULE;
        return $this->diskManager->upload($file, $module);
    }
}
