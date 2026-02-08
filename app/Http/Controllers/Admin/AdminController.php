<?php
namespace App\Http\Controllers\Admin;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Admin\StoreRequest;
use App\Http\Requests\Admin\Admin\UpdateRequest;
use App\Http\Resources\Admin\Admin\AdminCollectionResource;
use App\Http\Resources\Admin\Admin\AdminResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\AdminService;
use Illuminate\Http\Request;

/**
 * @group Admins
 * 
 * API endpoints for managing administrators.
 */
readonly class AdminController
{
    public function __construct(
        private AdminService $adminService
    ) {}

    /**
     * Get list of admins
     * 
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term. Example: "admin"
     * @queryParam sort string Sort field. Example: "name"
     * @queryParam order string Sort direction (asc/desc). Example: "asc"
     * 
     * @response 200 {"data": [{"id": 1, "name": "Admin", "email": "admin@example.com", ...}]}
     */
    public function index(Request $request): AdminCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new AdminCollectionResource($this->adminService->list($context)));
    }

    /**
     * Get admin by ID
     * 
     * @urlParam id int required Admin ID. Example: 1
     * 
     * @response 200 {"id": 1, "name": "Admin", "email": "admin@example.com", "phone": "0123456789", "status": "active", "avatar": "http://...", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Admin with ID [1] was not found."}
     * 
     * @throws NotFoundException
     */
    public function show(int $id): AdminResource
    {
        return new AdminResource($this->adminService->show($id));
    }

    /**
     * Create new admin
     * 
     * @bodyParam name string required Admin name. Example: "Admin"
     * @bodyParam email string required Unique email address. Example: "admin@example.com"
     * @bodyParam password string required Password (min 8 characters). Example: "password123"
     * @bodyParam phone string nullable Phone number (9-15 characters). Example: "0123456789"
     * @bodyParam status string required Status (active/inactive). Example: "active"
     * @bodyParam avatar file nullable Admin avatar image
     * 
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->adminService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * Update admin
     * 
     * @urlParam id int required Admin ID. Example: 1
     * @bodyParam name string required Admin name. Example: "Admin"
     * @bodyParam email string required Unique email address. Example: "admin@example.com"
     * @bodyParam password string nullable Password (min 8 characters). Example: "password123"
     * @bodyParam phone string nullable Phone number (9-15 characters). Example: "0123456789"
     * @bodyParam status string required Status (active/inactive). Example: "active"
     * @bodyParam avatar file nullable Admin avatar image
     * 
     * @response 200 {"id": 1, "name": "Admin", "email": "admin@example.com", ...}
     * @response 404 {"message": "The requested Admin with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     * 
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): AdminResource
    {
        return new AdminResource($this->adminService->update($id, $request->validated()));
    }

    /**
     * Delete admin
     * 
     * @urlParam id int required Admin ID. Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Admin with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->adminService->destroy($id);
        return new SuccessResponse([]);
    }
}
