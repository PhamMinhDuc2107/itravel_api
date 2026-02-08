<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Consultation\StoreRequest;
use App\Http\Requests\Admin\Consultation\UpdateRequest;
use App\Http\Resources\Admin\Consultation\ConsultationCollectionResource;
use App\Http\Resources\Admin\Consultation\ConsultationResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\ConsultationService;
use Illuminate\Http\Request;

/**
 * @group Consultations
 * 
 * API endpoints for managing consultation requests.
 */
readonly class ConsultationController
{
    public function __construct(
        private ConsultationService $consultationService
    ) {}

    /**
     * Get list of consultations
     * 
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term.
     * 
     * @response 200 {"data": [{"id": 1, "name": "John Doe", ...}]}
     */
    public function index(Request $request): ConsultationCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new ConsultationCollectionResource($this->consultationService->list($context)));
    }

    /**
     * Get consultation by ID
     * 
     * @urlParam id int required Consultation ID. Example: 1
     * 
     * @response 200 {"id": 1, "name": "John Doe", "phone": "0123456789", "email": "john@example.com", "productable_type": "App\\Model\\TourModel", "productable_id": 1, "product_name": "Tour Name", "metadata": {}, "message": "...", "status": "pending", "user_id": 1, "staff_notes": "...", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Consultation with ID [1] was not found."}
     * 
     * @throws NotFoundException
     */
    public function show(int $id): ConsultationResource
    {
        return new ConsultationResource($this->consultationService->show($id));
    }

    /**
     * Create new consultation
     * 
     * @bodyParam name string required Customer name. Example: "John Doe"
     * @bodyParam phone string required Phone number (max 20). Example: "0123456789"
     * @bodyParam email string nullable Email address. Example: "john@example.com"
     * @bodyParam productable_type string nullable Productable type. Example: "App\\Model\\TourModel"
     * @bodyParam productable_id int nullable Productable ID. Example: 1
     * @bodyParam product_name string nullable Product name. Example: "Tour Name"
     * @bodyParam metadata array nullable Metadata (JSON object).
     * @bodyParam message string nullable Consultation message.
     * @bodyParam status string required Status (pending/contacted/resolved/cancelled). Example: "pending"
     * @bodyParam user_id int nullable User ID. Example: 1
     * @bodyParam staff_notes string nullable Staff notes.
     * 
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->consultationService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * Update consultation
     * 
     * @urlParam id int required Consultation ID. Example: 1
     * @bodyParam name string required Customer name. Example: "John Doe"
     * @bodyParam phone string required Phone number (max 20). Example: "0123456789"
     * @bodyParam email string nullable Email address. Example: "john@example.com"
     * @bodyParam productable_type string nullable Productable type. Example: "App\\Model\\TourModel"
     * @bodyParam productable_id int nullable Productable ID. Example: 1
     * @bodyParam product_name string nullable Product name. Example: "Tour Name"
     * @bodyParam metadata array nullable Metadata (JSON object).
     * @bodyParam message string nullable Consultation message.
     * @bodyParam status string required Status (pending/contacted/resolved/cancelled). Example: "contacted"
     * @bodyParam user_id int nullable User ID. Example: 1
     * @bodyParam staff_notes string nullable Staff notes.
     * 
     * @response 200 {"id": 1, "name": "John Doe", "phone": "0123456789", "email": "john@example.com", "productable_type": "App\\Model\\TourModel", "productable_id": 1, "product_name": "Tour Name", "metadata": {}, "message": "...", "status": "contacted", "user_id": 1, "staff_notes": "...", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Consultation with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     * 
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): ConsultationResource
    {
        return new ConsultationResource($this->consultationService->update($id, $request->validated()));
    }

    /**
     * Delete consultation
     * 
     * @urlParam id int required Consultation ID. Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Consultation with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->consultationService->destroy($id);
        return new SuccessResponse([]);
    }
}

