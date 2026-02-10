<?php

namespace App\Service\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\HotelReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

readonly class HotelReviewService
{
    public function __construct(
        private HotelReviewRepositoryInterface $hotelReviewRepository,
    ) {}

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->hotelReviewRepository->list($context, $searchFields);
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $review = $this->hotelReviewRepository->find($id, ['hotel', 'user']);

        if (! $review) {
            throw new NotFoundException('HotelReview', $id);
        }

        return $review;
    }

    public function store(array $data): Model
    {
        return DB::transaction(fn () => $this->hotelReviewRepository->create($data));
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $review = $this->hotelReviewRepository->find($id);

        if (! $review) {
            throw new NotFoundException('HotelReview', $id);
        }

        return DB::transaction(fn () => $this->hotelReviewRepository->update($id, $data));
    }

    /**
     * @throws NotFoundException
     */
    public function destroy(int $id): bool
    {
        $review = $this->hotelReviewRepository->find($id);

        if (! $review) {
            throw new NotFoundException('HotelReview', $id);
        }

        return DB::transaction(fn () => $this->hotelReviewRepository->delete($id));
    }
}


