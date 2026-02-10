<?php

namespace App\Service\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\TourRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class TourService
{
    public function __construct(
        private TourRepositoryInterface $tourRepository,
    ) {}

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->tourRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $tour = $this->tourRepository->find($id, ['category', 'departureLocation', 'destinationLocation']);

        if (! $tour) {
            throw new NotFoundException('Tour', $id);
        }

        return $tour;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->tourRepository->create($data);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $tour = $this->tourRepository->find($id);

        if (! $tour) {
            throw new NotFoundException('Tour', $id);
        }

        return DB::transaction(function () use ($id, $data) {
            return $this->tourRepository->update($id, $data);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function destroy(int $id): bool
    {
        $tour = $this->tourRepository->find($id);

        if (! $tour) {
            throw new NotFoundException('Tour', $id);
        }

        return DB::transaction(function () use ($id) {
            return $this->tourRepository->delete($id);
        });
    }
}


