<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Hotel\StoreRequest;
use App\Http\Requests\Admin\Hotel\UpdateRequest;
use App\Http\Resources\Admin\Hotel\HotelCollectionResource;
use App\Http\Resources\Admin\Hotel\HotelResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\HotelService;
use Illuminate\Http\Request;

/**
 * @group Hotels
 *
 * API endpoints for managing hotels.
 */
readonly class HotelController
{
    public function __construct(
        private HotelService $hotelService
    ) {}

    /**
     * Get list of hotels
     *
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term. Example: "Resort"
     * @queryParam sort string Sort field. Example: "created_at"
     * @queryParam order string Sort direction (asc/desc). Example: "desc"
     *
     * @response 200 {"data": [{"id": 1, "name": "Khách sạn Grand Hà Nội", ...}]}
     */
    public function index(Request $request): HotelCollectionResource
    {
        $context = QueryContext::fromRequest($request);

        return new HotelCollectionResource($this->hotelService->list($context));
    }

    /**
     * Get hotel by ID
     *
     * @urlParam id int required Hotel ID. Example: 1
     *
     * @response 200 {"id": 1, "name": "Khách sạn Grand Hà Nội", ...}
     * @response 404 {"message": "The requested Hotel with ID [1] was not found."}
     *
     * @throws NotFoundException
     */
    public function show(int $id): HotelResource
    {
        return new HotelResource($this->hotelService->show($id));
    }

    /**
     * Create new hotel
     *
     * @bodyParam name string required Hotel name. Example: "Khách sạn Grand Hà Nội"
     * @bodyParam slug string required Unique slug. Example: "khach-san-grand-ha-noi"
     * @bodyParam hotel_type_id int required Hotel type ID. Example: 1
     * @bodyParam location_id int required Location ID. Example: 1
     * @bodyParam address string nullable Address.
     * @bodyParam latitude number nullable Latitude. Example: 21.027764
     * @bodyParam longitude number nullable Longitude. Example: 105.834160
     * @bodyParam image file nullable Main image file.
     * @bodyParam gallery array nullable Gallery image files.
     * @bodyParam star_rating int nullable Star rating (0-5). Example: 5
     * @bodyParam price_from number nullable Minimum price. Example: 1200000
     * @bodyParam excerpt string nullable Short description.
     * @bodyParam content string nullable Content.
     * @bodyParam policies string nullable Policies.
     * @bodyParam email string nullable Email. Example: "hotel@example.com"
     * @bodyParam phone string nullable Phone. Example: "0123456789"
     * @bodyParam website string nullable Website URL. Example: "https://hotel.example.com"
     * @bodyParam check_in_time string nullable Check-in time. Example: "14:00"
     * @bodyParam check_out_time string nullable Check-out time. Example: "12:00"
     * @bodyParam is_featured int nullable Featured flag (0 or 1). Example: 0
     * @bodyParam view_count int nullable Initial view count. Example: 0
     * @bodyParam status int required Status (ActiveStateEnum). Example: 1
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     * @bodyParam meta_keywords string nullable Meta keywords (max 255).
     * @bodyParam amenity_ids array nullable Amenity IDs. Example: [1,2,3]
     *
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->hotelService->store($request->validated());

        return new SuccessResponse([]);
    }

    /**
     * Update hotel
     *
     * @urlParam id int required Hotel ID. Example: 1
     * @bodyParam name string required Hotel name. Example: "Khách sạn Grand Hà Nội"
     * @bodyParam slug string required Unique slug. Example: "khach-san-grand-ha-noi"
     * @bodyParam hotel_type_id int required Hotel type ID. Example: 1
     * @bodyParam location_id int required Location ID. Example: 1
     * @bodyParam address string nullable Address.
     * @bodyParam latitude number nullable Latitude. Example: 21.027764
     * @bodyParam longitude number nullable Longitude. Example: 105.834160
     * @bodyParam image file nullable Main image file.
     * @bodyParam gallery array nullable Gallery image files.
     * @bodyParam star_rating int nullable Star rating (0-5). Example: 5
     * @bodyParam price_from number nullable Minimum price. Example: 1200000
     * @bodyParam excerpt string nullable Short description.
     * @bodyParam content string nullable Content.
     * @bodyParam policies string nullable Policies.
     * @bodyParam email string nullable Email. Example: "hotel@example.com"
     * @bodyParam phone string nullable Phone. Example: "0123456789"
     * @bodyParam website string nullable Website URL. Example: "https://hotel.example.com"
     * @bodyParam check_in_time string nullable Check-in time. Example: "14:00"
     * @bodyParam check_out_time string nullable Check-out time. Example: "12:00"
     * @bodyParam is_featured int nullable Featured flag (0 or 1). Example: 0
     * @bodyParam view_count int nullable Initial view count. Example: 0
     * @bodyParam status int required Status (ActiveStateEnum). Example: 1
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     * @bodyParam meta_keywords string nullable Meta keywords (max 255).
     * @bodyParam amenity_ids array nullable Amenity IDs. Example: [1,2,3]
     *
     * @response 200 {"id": 1, "name": "Khách sạn Grand Hà Nội", ...}
     * @response 404 {"message": "The requested Hotel with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     *
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): HotelResource
    {
        return new HotelResource($this->hotelService->update($id, $request->validated()));
    }

    /**
     * Delete hotel
     *
     * @urlParam id int required Hotel ID. Example: 1
     *
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Hotel with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->hotelService->destroy($id);

        return new SuccessResponse([]);
    }
}


