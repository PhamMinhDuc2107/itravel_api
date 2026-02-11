<?php

namespace App\Service\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\ConsultationRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class ConsultationService
{
    public function __construct(
        private ConsultationRepositoryInterface $consultationRepository,
    ) {
    }

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->consultationRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $consultation = $this->consultationRepository->find($id);

        if (!$consultation) {
            throw new NotFoundException('Consultation', $id);
        }

        return $consultation;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->consultationRepository->create($data);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $consultation = $this->consultationRepository->find($id);

        if (!$consultation) {
            throw new NotFoundException('Consultation', $id);
        }

        return DB::transaction(function () use ($id, $data) {
            return $this->consultationRepository->update($id, $data);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function destroy(int $id): bool
    {
        $consultation = $this->consultationRepository->find($id);

        if (!$consultation) {
            throw new NotFoundException('Consultation', $id);
        }

        return DB::transaction(function () use ($id) {
            return $this->consultationRepository->delete($id);
        });
    }

    public function destroyMultiple(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            return $this->consultationRepository->deleteMultiple($ids);
        });
    }
}

