<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Tour\StoreRequest;
use App\Http\Requests\Admin\Tour\UpdateRequest;
use App\Http\Requests\Admin\Tour\ImportTourRequest;
use App\Http\Requests\Admin\BulkDestroyRequest;
use App\Http\Resources\Admin\Tour\TourCollectionResource;
use App\Http\Resources\Admin\Tour\TourResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\TourService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Tours
 *
 * API endpoints for managing tours.
 */
readonly class TourController
{
    public function __construct(
        private TourService $tourService
    ) {
    }

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

    /**
     * Bulk delete tours
     *
     * Delete multiple tours at once by providing an array of IDs.
     *
     * @bodyParam ids int[] required Array of tour IDs to delete. Example: [1, 2, 3]
     *
     * @response 200 {"message": "Success", "data": {"deleted_count": 3}}
     * @response 422 {"message": "Validation error", "errors": {"ids": ["The ids field is required."]}}
     */
    public function bulkDestroy(BulkDestroyRequest $request): SuccessResponse
    {
        $deleted = $this->tourService->destroyMultiple($request->validated('ids'));
        return new SuccessResponse(['deleted_count' => $deleted]);
    }

    /**
     * Import tours from Excel
     *
     * Upload a multi-sheet Excel file (.xlsx, .xls, .csv) containing tour data.
     * The file is processed in the background via a queue job.
     * API responds immediately without waiting for import to complete.
     *
     * Excel must have 3 sheets:
     * - Sheet 1 "Tours": tour data (code, name, slug, category_id, locations, prices, etc.)
     * - Sheet 2 "Lịch trình": itineraries linked by tour_code (day_number, title, content)
     * - Sheet 3 "Ngày khởi hành": departures linked by tour_code (start_date, prices, stock)
     *
     * Image and gallery fields are skipped during import.
     * Use `php artisan tour:generate-template` to get a sample file.
     *
     * @bodyParam file file required Excel file (.xlsx, .xls, .csv). Max 10MB.
     *
     * @response 200 {"data": {"message": "Import queued for background processing", "file": "tour_import_20260211175400_abc123.xlsx"}}
     * @response 422 {"message": "Validation error", "errors": {"file": ["The file field is required."]}}
     */
    public function import(ImportTourRequest $request): SuccessResponse
    {
        $result = $this->tourService->import($request->file('file'));

        return new SuccessResponse($result);
    }

    /**
     * Download import template
     *
     * Download the Excel template file for bulk tour import.
     * The template contains 4 sheets:
     *
     * **Sheet 1 - "Tours":** Main tour data with sample rows.
     * Columns: code (required), name (required), slug (auto-generated if empty),
     * category_id, departure_location_id (required), destination_location_id (required),
     * duration_days (required), duration_nights, price_adult (required), price_child,
     * price_infant, excerpt, overview, policy, included, excluded,
     * status (0=Draft, 1=Published, 2=Closed, 3=Hidden), meta_title, meta_description.
     *
     * **Sheet 2 - "Lịch trình" (Itineraries):** Tour itineraries linked by tour_code.
     * Columns: tour_code (required, must match code in Sheet 1), day_number (required),
     * position, title (required), content.
     *
     * **Sheet 3 - "Ngày khởi hành" (Departures):** Tour departures linked by tour_code.
     * Columns: tour_code (required), start_date (required, YYYY-MM-DD),
     * price_adult (required), original_price_adult, price_child, original_price_child,
     * price_infant, original_price_infant, stock (required), booked,
     * status (available/sold_out/closed).
     *
     * **Sheet 4 - "Hướng dẫn":** Detailed instructions for each column.
     *
     * @response 200 file Binary Excel file (.xlsx)
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $templatePath = $this->tourService->generateTemplate();

        return response()->download(
            $templatePath,
            'tour_import_template.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }
}


