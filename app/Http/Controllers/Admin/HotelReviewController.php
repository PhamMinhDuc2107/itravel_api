<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\HotelReview\StoreRequest;
use App\Http\Requests\Admin\HotelReview\UpdateRequest;
use App\Http\Requests\Admin\BulkDestroyRequest;
use App\Http\Resources\Admin\HotelReview\HotelReviewCollectionResource;
use App\Http\Resources\Admin\HotelReview\HotelReviewResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\HotelReviewService;
use Illuminate\Http\Request;

/**
 * @group Hotel Reviews
 *
 * API endpoints for managing hotel reviews.
 */
readonly class HotelReviewController
{
    public function __construct(
        private HotelReviewService $hotelReviewService
    ) {
    }

    /**
     * Get list of hotel reviews
     *
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term. Example: "Nguyễn"
     * @queryParam sort string Sort field. Example: "created_at"
     * @queryParam order string Sort direction (asc/desc). Example: "desc"
     *
     * @response 200 {"data": [{"id": 1, "name": "Nguyễn Văn A", ...}]}
     */
    public function index(Request $request): HotelReviewCollectionResource
    {
        $context = QueryContext::fromRequest($request);

        return new HotelReviewCollectionResource($this->hotelReviewService->list($context));
    }

    /**
     * Get hotel review by ID
     *
     * @urlParam id int required Hotel review ID. Example: 1
     *
     * @response 200 {"id": 1, "hotel_id": 1, "name": "Nguyễn Văn A", ...}
     * @response 404 {"message": "The requested HotelReview with ID [1] was not found."}
     *
     * @throws NotFoundException
     */
    public function show(int $id): HotelReviewResource
    {
        return new HotelReviewResource($this->hotelReviewService->show($id));
    }

    /**
     * Create new hotel review
     *
     * @bodyParam hotel_id int required Hotel ID. Example: 1
     * @bodyParam user_id int nullable User ID. Example: 1
     * @bodyParam name string required Reviewer name. Example: "Nguyễn Văn A"
     * @bodyParam email string required Reviewer email. Example: "user@example.com"
     * @bodyParam rating int required Rating (1-5). Example: 5
     * @bodyParam comment string nullable Comment content.
     * @bodyParam images array nullable Image paths. Example: ["images/review1.jpg"]
     * @bodyParam status int required Status (ActiveStateEnum). Example: 0
     *
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->hotelReviewService->store($request->validated());

        return new SuccessResponse([]);
    }

    /**
     * Update hotel review
     *
     * @urlParam id int required Hotel review ID. Example: 1
     * @bodyParam hotel_id int required Hotel ID. Example: 1
     * @bodyParam user_id int nullable User ID. Example: 1
     * @bodyParam name string required Reviewer name. Example: "Nguyễn Văn A"
     * @bodyParam email string required Reviewer email. Example: "user@example.com"
     * @bodyParam rating int required Rating (1-5). Example: 5
     * @bodyParam comment string nullable Comment content.
     * @bodyParam images array nullable Image paths. Example: ["images/review1.jpg"]
     * @bodyParam status int required Status (ActiveStateEnum). Example: 0
     *
     * @response 200 {"id": 1, "hotel_id": 1, "name": "Nguyễn Văn A", ...}
     * @response 404 {"message": "The requested HotelReview with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     *
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): HotelReviewResource
    {
        return new HotelReviewResource($this->hotelReviewService->update($id, $request->validated()));
    }

    /**
     * Delete hotel review
     *
     * @urlParam id int required Hotel review ID. Example: 1
     *
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested HotelReview with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->hotelReviewService->destroy($id);

        return new SuccessResponse([]);
    }

    /**
     * Bulk delete hotel reviews
     *
     * Delete multiple hotel reviews at once by providing an array of IDs.
     *
     * @bodyParam ids int[] required Array of hotel review IDs to delete. Example: [1, 2, 3]
     *
     * @response 200 {"message": "Success", "data": {"deleted_count": 3}}
     * @response 422 {"message": "Validation error", "errors": {"ids": ["The ids field is required."]}}
     */
    public function bulkDestroy(BulkDestroyRequest $request): SuccessResponse
    {
        $deleted = $this->hotelReviewService->destroyMultiple($request->validated('ids'));
        return new SuccessResponse(['deleted_count' => $deleted]);
    }
}


