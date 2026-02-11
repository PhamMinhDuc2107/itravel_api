<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Banner\StoreRequest;
use App\Http\Requests\Admin\Banner\UpdateRequest;
use App\Http\Requests\Admin\BulkDestroyRequest;
use App\Http\Resources\Admin\Banner\BannerCollectionResource;
use App\Http\Resources\Admin\Banner\BannerResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\BannerService;
use Illuminate\Http\Request;

/**
 * @group Banners
 * 
 * API endpoints for managing banners.
 */
readonly class BannerController
{
    public function __construct(
        private BannerService $bannerService
    ) {
    }

    /**
     * Get list of banners
     * 
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term. Example: "promo"
     * @queryParam sort string Sort field. Example: "position"
     * @queryParam order string Sort direction (asc/desc). Example: "asc"
     * 
     * @response 200 {"data": [{"id": 1, "name": "Promo Banner", ...}]}
     */
    public function index(Request $request): BannerCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new BannerCollectionResource($this->bannerService->list($context)));
    }

    /**
     * Get banner by ID
     * 
     * @urlParam id int required Banner ID. Example: 1
     * 
     * @response 200 {"id": 1, "name": "Promo Banner", "image": "http://...", "mobile_image": "http://...", "link": "https://...", "target": "_blank", "description": "...", "type": "home", "position": 0, "status": 1, "start_at": "2024-01-01 00:00:00", "end_at": "2024-12-31 23:59:59", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Banner with ID [1] was not found."}
     * 
     * @throws NotFoundException
     */
    public function show(int $id): BannerResource
    {
        return new BannerResource($this->bannerService->show($id));
    }

    /**
     * Create new banner
     * 
     * @bodyParam name string required Banner name. Example: "Promo Banner"
     * @bodyParam image file required Banner image
     * @bodyParam mobile_image file nullable Mobile banner image
     * @bodyParam link string nullable Link URL (max 500). Example: "https://example.com"
     * @bodyParam target string nullable Link target (_self or _blank). Example: "_blank"
     * @bodyParam description string nullable Banner description.
     * @bodyParam type string nullable Banner type. Example: "home"
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (0 or 1). Example: 1
     * @bodyParam start_at date nullable Start date. Example: "2024-01-01"
     * @bodyParam end_at date nullable End date (must be after or equal to start_at). Example: "2024-12-31"
     * 
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->bannerService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * Update banner
     * 
     * @urlParam id int required Banner ID. Example: 1
     * @bodyParam name string required Banner name. Example: "Promo Banner"
     * @bodyParam image file nullable Banner image
     * @bodyParam mobile_image file nullable Mobile banner image
     * @bodyParam link string nullable Link URL (max 500). Example: "https://example.com"
     * @bodyParam target string nullable Link target (_self or _blank). Example: "_blank"
     * @bodyParam description string nullable Banner description.
     * @bodyParam type string nullable Banner type. Example: "home"
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (0 or 1). Example: 1
     * @bodyParam start_at date nullable Start date. Example: "2024-01-01"
     * @bodyParam end_at date nullable End date (must be after or equal to start_at). Example: "2024-12-31"
     * 
     * @response 200 {"id": 1, "name": "Promo Banner", ...}
     * @response 404 {"message": "The requested Banner with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     * 
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): BannerResource
    {
        return new BannerResource($this->bannerService->update($id, $request->validated()));
    }

    /**
     * Delete banner
     * 
     * @urlParam id int required Banner ID. Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Banner with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->bannerService->destroy($id);
        return new SuccessResponse([]);
    }

    /**
     * Bulk delete banners
     *
     * Delete multiple banners at once by providing an array of IDs.
     *
     * @bodyParam ids int[] required Array of banner IDs to delete. Example: [1, 2, 3]
     *
     * @response 200 {"message": "Success", "data": {"deleted_count": 3}}
     * @response 422 {"message": "Validation error", "errors": {"ids": ["The ids field is required."]}}
     */
    public function bulkDestroy(BulkDestroyRequest $request): SuccessResponse
    {
        $deleted = $this->bannerService->destroyMultiple($request->validated('ids'));
        return new SuccessResponse(['deleted_count' => $deleted]);
    }
}

