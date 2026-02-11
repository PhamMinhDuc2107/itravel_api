<?php

namespace App\Service\Admin;

use App\Constant\UploadConstant;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\BankAccountRepositoryInterface;
use App\Support\File\DiskManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class BankAccountService
{
    public function __construct(
        private BankAccountRepositoryInterface $bankAccountRepository,
        private DiskManager $diskManager,
    ) {
    }

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->bankAccountRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $bankAccount = $this->bankAccountRepository->find($id);

        if (!$bankAccount) {
            throw new NotFoundException('Bank Account', $id);
        }

        return $bankAccount;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $uploadedLogoPath = null;
            $uploadedQrCodePath = null;

            try {
                if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
                    $uploadedLogoPath = $this->uploadLogo($data['logo']);
                    $data['logo'] = $uploadedLogoPath;
                }

                if (isset($data['qr_code']) && $data['qr_code'] instanceof UploadedFile) {
                    $uploadedQrCodePath = $this->uploadQrCode($data['qr_code']);
                    $data['qr_code'] = $uploadedQrCodePath;
                }

                return $this->bankAccountRepository->create($data);

            } catch (\Exception $e) {
                if ($uploadedLogoPath) {
                    $this->diskManager->delete($uploadedLogoPath);
                }
                if ($uploadedQrCodePath) {
                    $this->diskManager->delete($uploadedQrCodePath);
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
        $bankAccount = $this->bankAccountRepository->find($id);

        if (!$bankAccount) {
            throw new NotFoundException('Bank Account', $id);
        }

        return DB::transaction(function () use ($id, $bankAccount, $data) {
            $uploadedLogoPath = null;
            $uploadedQrCodePath = null;
            $oldLogo = $bankAccount->logo;
            $oldQrCode = $bankAccount->qr_code;

            try {
                if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
                    if ($oldLogo) {
                        $this->diskManager->delete($oldLogo);
                    }

                    $uploadedLogoPath = $this->uploadLogo($data['logo']);
                    $data['logo'] = $uploadedLogoPath;
                }

                if (isset($data['qr_code']) && $data['qr_code'] instanceof UploadedFile) {
                    if ($oldQrCode) {
                        $this->diskManager->delete($oldQrCode);
                    }

                    $uploadedQrCodePath = $this->uploadQrCode($data['qr_code']);
                    $data['qr_code'] = $uploadedQrCodePath;
                }

                return $this->bankAccountRepository->update($id, $data);

            } catch (\Exception $e) {
                if ($uploadedLogoPath) {
                    $this->diskManager->delete($uploadedLogoPath);
                }
                if ($uploadedQrCodePath) {
                    $this->diskManager->delete($uploadedQrCodePath);
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
        $bankAccount = $this->bankAccountRepository->find($id);

        if (!$bankAccount) {
            throw new NotFoundException('Bank Account', $id);
        }

        return DB::transaction(function () use ($id, $bankAccount) {
            $deleted = $this->bankAccountRepository->delete($id);

            if ($deleted) {
                if ($bankAccount->logo) {
                    $this->diskManager->delete($bankAccount->logo);
                }
                if ($bankAccount->qr_code) {
                    $this->diskManager->delete($bankAccount->qr_code);
                }
            }

            return $deleted;
        });
    }

    public function destroyMultiple(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $accounts = $this->bankAccountRepository->findAllBy([['id', 'in', $ids]]);
            $deleted = $this->bankAccountRepository->deleteMultiple($ids);

            if ($deleted) {
                foreach ($accounts as $account) {
                    if ($account->logo) {
                        $this->diskManager->delete($account->logo);
                    }
                    if ($account->qr_code) {
                        $this->diskManager->delete($account->qr_code);
                    }
                }
            }

            return $deleted;
        });
    }

    private function uploadLogo(UploadedFile $file): string
    {
        $module = UploadConstant::DEFAULT_MODULE;
        return $this->diskManager->upload($file, $module);
    }

    private function uploadQrCode(UploadedFile $file): string
    {
        $module = UploadConstant::DEFAULT_MODULE;
        return $this->diskManager->upload($file, $module);
    }
}

