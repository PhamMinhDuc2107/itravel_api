<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Amenity\StoreRequest;
use App\Http\Requests\Admin\Amenity\UpdateRequest;
use App\Http\Requests\Admin\BulkDestroyRequest;
use App\Http\Resources\Admin\Amenity\AmenityCollectionResource;
use App\Http\Resources\Admin\Amenity\AmenityResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\AmenityService;
use Illuminate\Http\Request;

/**
 * @group Amenities
 *
 * API endpoints for managing amenities.
 */
readonly class AmenityController
{
    public function __construct(
        private AmenityService $amenityService
    ) {
    }

    /**
     * Get list of amenities
     *
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term. Example: "wifi"
     * @queryParam sort string Sort field. Example: "name"
     * @queryParam order string Sort direction (asc/desc). Example: "asc"
     *
     * @response 200 {"data": [{"id": 1, "name": "Wifi miễn phí", ...}]}
     */
    public function index(Request $request): AmenityCollectionResource
    {
        $context = QueryContext::fromRequest($request);

        return new AmenityCollectionResource($this->amenityService->list($context));
    }

    /**
     * Get amenity by ID
     *
     * @urlParam id int required Amenity ID. Example: 1
     *
     * @response 200 {"id": 1, "name": "Wifi miễn phí", "code": "wifi", ...}
     * @response 404 {"message": "The requested Amenity with ID [1] was not found."}
     *
     * @throws NotFoundException
     */
    public function show(int $id): AmenityResource
    {
        return new AmenityResource($this->amenityService->show($id));
    }

    /**
     * Create new amenity
     *
     * @bodyParam name string required Amenity name. Example: "Wifi miễn phí"
     * @bodyParam code string required Unique code. Example: "wifi"
     * @bodyParam icon string nullable Icon class or path. Example: "fa fa-wifi"
     * @bodyParam type string required Amenity type (general/room/bathroom/dining/entertainment/other). Example: "general"
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (ActiveStateEnum). Example: 1
     *
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->amenityService->store($request->validated());

        return new SuccessResponse([]);
    }

    /**
     * Update amenity
     *
     * @urlParam id int required Amenity ID. Example: 1
     * @bodyParam name string required Amenity name. Example: "Wifi miễn phí"
     * @bodyParam code string required Unique code. Example: "wifi"
     * @bodyParam icon string nullable Icon class or path. Example: "fa fa-wifi"
     * @bodyParam type string required Amenity type (general/room/bathroom/dining/entertainment/other). Example: "general"
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (ActiveStateEnum). Example: 1
     *
     * @response 200 {"id": 1, "name": "Wifi miễn phí", "code": "wifi", ...}
     * @response 404 {"message": "The requested Amenity with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     *
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): AmenityResource
    {
        return new AmenityResource($this->amenityService->update($id, $request->validated()));
    }

    /**
     * Delete amenity
     *
     * @urlParam id int required Amenity ID. Example: 1
     *
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Amenity with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->amenityService->destroy($id);

        return new SuccessResponse([]);
    }

    /**
     * Bulk delete amenities
     *
     * Delete multiple amenities at once by providing an array of IDs.
     *
     * @bodyParam ids int[] required Array of amenity IDs to delete. Example: [1, 2, 3]
     *
     * @response 200 {"message": "Success", "data": {"deleted_count": 3}}
     * @response 422 {"message": "Validation error", "errors": {"ids": ["The ids field is required."]}}
     */
    public function bulkDestroy(BulkDestroyRequest $request): SuccessResponse
    {
        $deleted = $this->amenityService->destroyMultiple($request->validated('ids'));
        return new SuccessResponse(['deleted_count' => $deleted]);
    }
}


