<?php

namespace App\Service\Admin;

use App\Constant\UploadConstant;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\SupportTeamRepositoryInterface;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class SupportTeamService
{
    public function __construct(
        private SupportTeamRepositoryInterface $supportTeamRepository,
        private DiskManager $diskManager,
    ) {
    }

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->supportTeamRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $supportTeam = $this->supportTeamRepository->find($id);

        if (!$supportTeam) {
            throw new NotFoundException('Support Team', $id);
        }

        return $supportTeam;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $uploadedAvatarPath = null;

            try {
                if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                    $uploadedAvatarPath = $this->uploadAvatar($data['avatar']);
                    $data['avatar'] = $uploadedAvatarPath;
                }

                return $this->supportTeamRepository->create($data);

            } catch (\Exception $e) {
                if ($uploadedAvatarPath) {
                    $this->diskManager->delete($uploadedAvatarPath);
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
        $supportTeam = $this->supportTeamRepository->find($id);

        if (!$supportTeam) {
            throw new NotFoundException('Support Team', $id);
        }

        return DB::transaction(function () use ($id, $supportTeam, $data) {
            $uploadedAvatarPath = null;
            $oldAvatar = $supportTeam->avatar;

            try {
                if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                    if ($oldAvatar) {
                        $this->diskManager->delete($oldAvatar);
                    }

                    $uploadedAvatarPath = $this->uploadAvatar($data['avatar']);
                    $data['avatar'] = $uploadedAvatarPath;
                }

                return $this->supportTeamRepository->update($id, $data);

            } catch (\Exception $e) {
                if ($uploadedAvatarPath) {
                    $this->diskManager->delete($uploadedAvatarPath);
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
        $supportTeam = $this->supportTeamRepository->find($id);

        if (!$supportTeam) {
            throw new NotFoundException('Support Team', $id);
        }

        return DB::transaction(function () use ($id, $supportTeam) {
            $deleted = $this->supportTeamRepository->delete($id);

            if ($deleted && $supportTeam->avatar) {
                $this->diskManager->delete($supportTeam->avatar);
            }

            return $deleted;
        });
    }

    public function destroyMultiple(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $members = $this->supportTeamRepository->findAllBy([['id', 'in', $ids]]);
            $deleted = $this->supportTeamRepository->deleteMultiple($ids);

            if ($deleted) {
                foreach ($members as $member) {
                    if ($member->avatar) {
                        $this->diskManager->delete($member->avatar);
                    }
                }
            }

            return $deleted;
        });
    }

    private function uploadAvatar(UploadedFile $file): string
    {
        $module = UploadConstant::AVATAR_MODULE;
        return $this->diskManager->upload($file, $module);
    }
}

