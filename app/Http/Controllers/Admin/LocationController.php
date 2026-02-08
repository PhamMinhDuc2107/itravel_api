<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Location\StoreRequest;
use App\Http\Requests\Admin\Location\UpdateRequest;
use App\Http\Resources\Admin\Location\LocationCollectionResource;
use App\Http\Resources\Admin\Location\LocationResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\LocationService;
use Illuminate\Http\Request;

/**
 * @group Locations
 * 
 * API endpoints for managing locations (countries, regions, provinces, attractions).
 */
readonly class LocationController
{
    public function __construct(
        private LocationService $locationService
    ) {}

    /**
     * Get list of locations
     * 
     * Get a paginated list of locations with filtering and sorting options.
     * 
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term. Example: "Ha Noi"
     * @queryParam sort string Sort field. Example: "name"
     * @queryParam order string Sort direction (asc/desc). Example: "asc"
     * 
     * @response 200 {"data": [{"id": 1, "name": "Ha Noi", ...}]}
     */
    public function index(Request $request): LocationCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new LocationCollectionResource($this->locationService->list($context)));
    }

    /**
     * Get location by ID
     * 
     * Get detailed information about a specific location.
     * 
     * @urlParam id int required Location ID. Example: 1
     * 
     * @response 200 {"id": 1, "name": "Ha Noi", "slug": "ha-noi", "parent_id": null, "description": "...", "content": "...", "image": "http://...", "type": "province", "display_home": 0, "is_feature": 0, "is_departure": 0, "is_destination": 1, "position": 0, "status": "active", "meta_title": "...", "meta_description": "...", "meta_keywords": "...", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Location with ID [1] was not found."}
     * 
     * @throws NotFoundException
     */
    public function show(int $id): LocationResource
    {
        return new LocationResource($this->locationService->show($id));
    }

    /**
     * Create new location
     * 
     * Create a new location with the provided data.
     * 
     * @bodyParam name string required Location name. Example: "Ha Noi"
     * @bodyParam slug string required Unique slug. Example: "ha-noi"
     * @bodyParam parent_id int nullable Parent location ID. Example: null
     * @bodyParam description string nullable Location description.
     * @bodyParam content string nullable Location content.
     * @bodyParam image file nullable Location image
     * @bodyParam type string required Location type (country/region/province/attraction). Example: "province"
     * @bodyParam display_home int nullable Display on home (0 or 1). Example: 0
     * @bodyParam is_feature int nullable Is featured (0 or 1). Example: 0
     * @bodyParam is_departure int nullable Is departure location (0 or 1). Example: 0
     * @bodyParam is_destination int nullable Is destination location (0 or 1). Example: 1
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status string required Status (active/inactive). Example: "active"
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     * @bodyParam meta_keywords string nullable Meta keywords (max 255).
     * 
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->locationService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * Update location
     * 
     * Update an existing location by ID.
     * 
     * @urlParam id int required Location ID. Example: 1
     * @bodyParam name string required Location name. Example: "Ha Noi"
     * @bodyParam slug string required Unique slug. Example: "ha-noi"
     * @bodyParam parent_id int nullable Parent location ID (cannot be itself). Example: null
     * @bodyParam description string nullable Location description.
     * @bodyParam content string nullable Location content.
     * @bodyParam image file nullable Location image
     * @bodyParam type string required Location type (country/region/province/attraction). Example: "province"
     * @bodyParam display_home int nullable Display on home (0 or 1). Example: 0
     * @bodyParam is_feature int nullable Is featured (0 or 1). Example: 0
     * @bodyParam is_departure int nullable Is departure location (0 or 1). Example: 0
     * @bodyParam is_destination int nullable Is destination location (0 or 1). Example: 1
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status string required Status (active/inactive). Example: "active"
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     * @bodyParam meta_keywords string nullable Meta keywords (max 255).
     * 
     * @response 200 {"id": 1, "name": "Ha Noi", "slug": "ha-noi", "parent_id": null, "description": "...", "content": "...", "image": "http://...", "type": "province", "display_home": 0, "is_feature": 0, "is_departure": 0, "is_destination": 1, "position": 0, "status": "active", "meta_title": "...", "meta_description": "...", "meta_keywords": "...", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Location with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     * 
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): LocationResource
    {
        return new LocationResource($this->locationService->update($id, $request->validated()));
    }

    /**
     * Delete location
     * 
     * Soft delete a location by ID.
     * 
     * @urlParam id int required Location ID. Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Location with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->locationService->destroy($id);
        return new SuccessResponse([]);
    }
}

