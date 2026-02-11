<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Booking\ExportRequest;
use App\Http\Requests\Admin\Booking\StoreRequest;
use App\Http\Requests\Admin\Booking\UpdateRequest;
use App\Http\Requests\Admin\BulkDestroyRequest;
use App\Http\Resources\Admin\Booking\BookingCollectionResource;
use App\Http\Resources\Admin\Booking\BookingResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\BookingService;
use App\Service\Admin\ExportBookingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Bookings
 *
 * API endpoints for managing bookings (orders) with nested items and passengers.
 */
readonly class BookingController
{
    public function __construct(
        private BookingService $bookingService,
        private ExportBookingService $exportBookingService,
    ) {
    }

    /**
     * Get list of bookings
     *
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term (code, customer name, phone, email). Example: "BK-20260210"
     * @queryParam sort string Sort field. Example: "created_at"
     * @queryParam order string Sort direction (asc/desc). Example: "desc"
     *
     * @response 200 {"data": [{"id": 1, "code": "BK-20260210-XXXX", "customer_name": "Nguyen Van A", ...}]}
     */
    public function index(Request $request): BookingCollectionResource
    {
        $context = QueryContext::fromRequest($request);

        return new BookingCollectionResource($this->bookingService->list($context));
    }

    /**
     * Get booking by ID
     *
     * @urlParam id int required Booking ID. Example: 1
     *
     * @response 200 {"id": 1, "code": "BK-20260210-XXXX", "customer_name": "Nguyen Van A", "items": [...], "logs": [...]}
     * @response 404 {"message": "The requested Booking with ID [1] was not found."}
     *
     * @throws NotFoundException
     */
    public function show(int $id): BookingResource
    {
        return new BookingResource($this->bookingService->show($id));
    }

    /**
     * Create new booking
     *
     * Auto-generates booking code (BK-YYYYMMDDHHMMSS-XXXX).
     * Calculates subtotal and total_amount from items.
     * Creates nested items and passengers.
     * Logs "created" event.
     *
     * @bodyParam user_id int nullable User ID. Example: 1
     * @bodyParam customer_name string required Customer name. Example: "Nguyen Van A"
     * @bodyParam customer_phone string required Customer phone. Example: "0912345678"
     * @bodyParam customer_email string required Customer email. Example: "customer@example.com"
     * @bodyParam note string nullable Note.
     * @bodyParam discount number nullable Discount amount. Example: 100000
     * @bodyParam tax number nullable Tax amount. Example: 50000
     * @bodyParam status string required Booking status (pending/confirmed/processing/completed/cancelled/declined). Example: "pending"
     * @bodyParam payment_status string required Payment status (unpaid/partially_paid/paid/refunded). Example: "unpaid"
     * @bodyParam payment_method string required Payment method (cod/bank_transfer/vnpay/momo/credit_card). Example: "bank_transfer"
     * @bodyParam source string nullable Source (website/mobile_app/etc). Example: "website"
     * @bodyParam items array required Array of booking items (min 1). Example: [{"product_name": "Tour Ha Long", "quantity": 2, "price": 2000000, ...}]
     * @bodyParam items.*.productable_type string nullable Product type (App\Model\TourModel, etc). Example: "App\Model\TourModel"
     * @bodyParam items.*.productable_id int nullable Product ID. Example: 5
     * @bodyParam items.*.product_name string required Product name. Example: "Tour Ha Long 2N1D"
     * @bodyParam items.*.product_image string nullable Product image URL.
     * @bodyParam items.*.product_code string nullable Product code. Example: "TOUR-HL-001"
     * @bodyParam items.*.quantity int required Quantity (min 1). Example: 2
     * @bodyParam items.*.price number required Unit price. Example: 2000000
     * @bodyParam items.*.start_date date nullable Start date. Example: "2026-03-15"
     * @bodyParam items.*.end_date date nullable End date. Example: "2026-03-17"
     * @bodyParam items.*.options object nullable Additional options (JSON).
     * @bodyParam items.*.passengers array nullable Array of passengers for this item.
     * @bodyParam items.*.passengers.*.full_name string required Passenger name. Example: "Nguyen Van B"
     * @bodyParam items.*.passengers.*.dob date nullable Date of birth. Example: "1990-05-20"
     * @bodyParam items.*.passengers.*.gender string nullable Gender (male/female/other). Example: "male"
     * @bodyParam items.*.passengers.*.phone string nullable Phone. Example: "0987654321"
     * @bodyParam items.*.passengers.*.passport_number string nullable Passport number. Example: "N12345678"
     * @bodyParam items.*.passengers.*.passport_expiry date nullable Passport expiry. Example: "2030-12-31"
     * @bodyParam items.*.passengers.*.nationality string nullable Nationality. Example: "Vietnam"
     * @bodyParam items.*.passengers.*.type string required Passenger type (adult/child/infant). Example: "adult"
     *
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->bookingService->store($request->validated());

        return new SuccessResponse([]);
    }

    /**
     * Update booking
     *
     * Can update customer info, status, payment, items, etc.
     * Recalculates totals if items are updated.
     * Logs "updated", "status_changed", or "payment_status_changed" event.
     *
     * @urlParam id int required Booking ID. Example: 1
     * @bodyParam user_id int nullable User ID. Example: 1
     * @bodyParam customer_name string Customer name. Example: "Nguyen Van A"
     * @bodyParam customer_phone string Customer phone. Example: "0912345678"
     * @bodyParam customer_email string Customer email. Example: "customer@example.com"
     * @bodyParam note string nullable Note.
     * @bodyParam discount number nullable Discount amount. Example: 100000
     * @bodyParam tax number nullable Tax amount. Example: 50000
     * @bodyParam status string Booking status. Example: "confirmed"
     * @bodyParam payment_status string Payment status. Example: "partially_paid"
     * @bodyParam payment_method string Payment method. Example: "vnpay"
     * @bodyParam source string nullable Source. Example: "mobile_app"
     * @bodyParam items array Items array (same structure as store). If provided, replaces all existing items.
     *
     * @response 200 {"id": 1, "code": "BK-20260210-XXXX", "status": "confirmed", ...}
     * @response 404 {"message": "The requested Booking with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     *
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): BookingResource
    {
        return new BookingResource($this->bookingService->update($id, $request->validated()));
    }

    /**
     * Delete booking
     *
     * Soft-deletes booking, cascades to items and passengers.
     * Logs "deleted" event before deletion.
     *
     * @urlParam id int required Booking ID. Example: 1
     *
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Booking with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->bookingService->destroy($id);

        return new SuccessResponse([]);
    }

    /**
     * Bulk delete bookings
     *
     * Delete multiple bookings at once by providing an array of IDs.
     * Logs "deleted" event for each booking before deletion.
     *
     * @bodyParam ids int[] required Array of booking IDs to delete. Example: [1, 2, 3]
     *
     * @response 200 {"message": "Success", "data": {"deleted_count": 3}}
     * @response 422 {"message": "Validation error", "errors": {"ids": ["The ids field is required."]}}
     */
    public function bulkDestroy(BulkDestroyRequest $request): SuccessResponse
    {
        $deleted = $this->bookingService->destroyMultiple($request->validated('ids'));
        return new SuccessResponse(['deleted_count' => $deleted]);
    }

    /**
     * Export bookings to Excel
     *
     * Exports booking list and items to Excel file with 2 sheets.
     * Supports filtering by date range, status, payment status, and limit.
     * File name is auto-generated based on filters.
     *
     * @queryParam date_from date nullable Filter from date (YYYY-MM-DD). Example: "2026-02-01"
     * @queryParam date_to date nullable Filter to date (YYYY-MM-DD). Example: "2026-02-28"
     * @queryParam status string nullable Filter by booking status. Example: "confirmed"
     * @queryParam payment_status string nullable Filter by payment status. Example: "paid"
     * @queryParam limit int nullable Limit number of records (max 10000). Example: 100
     *
     * @response 200 file Binary Excel file (.xlsx)
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function export(ExportRequest $request): BinaryFileResponse
    {
        return $this->exportBookingService->exportToExcel($request->validated());
    }
}

