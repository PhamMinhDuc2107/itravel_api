<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Tour\StoreRequest;
use App\Http\Requests\Admin\Tour\UpdateRequest;
use App\Http\Resources\Admin\Tour\TourCollectionResource;
use App\Http\Resources\Admin\Tour\TourResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\TourService;
use Illuminate\Http\Request;

/**
 * @group Tours
 *
 * API endpoints for managing tours.
 */
readonly class TourController
{
    public function __construct(
        private TourService $tourService
    ) {}

    /**
     * Get list of tours
     *
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term. Example: "Hà Nội"
     * @queryParam sort string Sort field. Example: "created_at"
     * @queryParam order string Sort direction (asc/desc). Example: "desc"
     *
     * @response 200 {"data": [{"id": 1, "name": "Tour Hà Nội - Sapa", ...}]}
     */
    public function index(Request $request): TourCollectionResource
    {
        $context = QueryContext::fromRequest($request);

        return new TourCollectionResource($this->tourService->list($context));
    }

    /**
     * Get tour by ID
     *
     * @urlParam id int required Tour ID. Example: 1
     *
     * @response 200 {"id": 1, "code": "TOUR-XXXX", "name": "Tour Hà Nội - Sapa", ...}
     * @response 404 {"message": "The requested Tour with ID [1] was not found."}
     *
     * @throws NotFoundException
     */
    public function show(int $id): TourResource
    {
        return new TourResource($this->tourService->show($id));
    }

    /**
     * Create new tour
     *
     * @bodyParam code string required Unique tour code. Example: "TOUR-HNSP-3N2D"
     * @bodyParam name string required Tour name. Example: "Tour Hà Nội - Sapa 3 ngày 2 đêm"
     * @bodyParam slug string required Unique slug. Example: "tour-ha-noi-sapa-3n2d"
     * @bodyParam category_id int nullable Category ID. Example: 1
     * @bodyParam departure_location_id int required Departure location ID. Example: 1
     * @bodyParam destination_location_id int required Destination location ID (different from departure). Example: 2
     * @bodyParam duration_days int required Number of days (>=1). Example: 3
     * @bodyParam duration_nights int nullable Number of nights (>=0). Example: 2
     * @bodyParam is_recurring int nullable Recurring flag (0 or 1). Example: 0
     * @bodyParam recurring_days array nullable Recurring days of week (0-6). Example: [1,3,5]
     * @bodyParam price_adult number required Adult price. Example: 3500000
     * @bodyParam price_child number nullable Child price. Example: 2500000
     * @bodyParam price_infant number nullable Infant price. Example: 0
     * @bodyParam excerpt string nullable Short description.
     * @bodyParam overview string nullable Tour overview.
     * @bodyParam policy string nullable Policy content.
     * @bodyParam included string nullable Included services.
     * @bodyParam excluded string nullable Excluded services.
     * @bodyParam image file nullable Main image file.
     * @bodyParam gallery array nullable Gallery image files.
     * @bodyParam view_count int nullable Initial view count. Example: 0
     * @bodyParam position int nullable Position (>=0). Example: 0
     * @bodyParam status int required Status (TourStatusEnum). Example: 1
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     *
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->tourService->store($request->validated());

        return new SuccessResponse([]);
    }

    /**
     * Update tour
     *
     * @urlParam id int required Tour ID. Example: 1
     * @bodyParam code string required Unique tour code. Example: "TOUR-HNSP-3N2D"
     * @bodyParam name string required Tour name. Example: "Tour Hà Nội - Sapa 3 ngày 2 đêm"
     * @bodyParam slug string required Unique slug. Example: "tour-ha-noi-sapa-3n2d"
     * @bodyParam category_id int nullable Category ID. Example: 1
     * @bodyParam departure_location_id int required Departure location ID. Example: 1
     * @bodyParam destination_location_id int required Destination location ID (different from departure). Example: 2
     * @bodyParam duration_days int required Number of days (>=1). Example: 3
     * @bodyParam duration_nights int nullable Number of nights (>=0). Example: 2
     * @bodyParam is_recurring int nullable Recurring flag (0 or 1). Example: 0
     * @bodyParam recurring_days array nullable Recurring days of week (0-6). Example: [1,3,5]
     * @bodyParam price_adult number required Adult price. Example: 3500000
     * @bodyParam price_child number nullable Child price. Example: 2500000
     * @bodyParam price_infant number nullable Infant price. Example: 0
     * @bodyParam excerpt string nullable Short description.
     * @bodyParam overview string nullable Tour overview.
     * @bodyParam policy string nullable Policy content.
     * @bodyParam included string nullable Included services.
     * @bodyParam excluded string nullable Excluded services.
     * @bodyParam image file nullable Main image file.
     * @bodyParam gallery array nullable Gallery image files.
     * @bodyParam view_count int nullable Initial view count. Example: 0
     * @bodyParam position int nullable Position (>=0). Example: 0
     * @bodyParam status int required Status (TourStatusEnum). Example: 1
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     *
     * @response 200 {"id": 1, "code": "TOUR-HNSP-3N2D", "name": "Tour Hà Nội - Sapa", ...}
     * @response 404 {"message": "The requested Tour with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     *
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): TourResource
    {
        return new TourResource($this->tourService->update($id, $request->validated()));
    }

    /**
     * Delete tour
     *
     * @urlParam id int required Tour ID. Example: 1
     *
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Tour with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->tourService->destroy($id);

        return new SuccessResponse([]);
    }
}


