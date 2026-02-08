<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\SupportTeam\StoreRequest;
use App\Http\Requests\Admin\SupportTeam\UpdateRequest;
use App\Http\Resources\Admin\SupportTeam\SupportTeamCollectionResource;
use App\Http\Resources\Admin\SupportTeam\SupportTeamResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\SupportTeamService;
use Illuminate\Http\Request;

/**
 * @group Support Team
 * 
 * API endpoints for managing support team members.
 */
readonly class SupportTeamController
{
    public function __construct(
        private SupportTeamService $supportTeamService
    ) {}

    /**
     * Get list of support team members
     * 
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term.
     * 
     * @response 200 {"data": [{"id": 1, "name": "John Doe", ...}]}
     */
    public function index(Request $request): SupportTeamCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new SupportTeamCollectionResource($this->supportTeamService->list($context)));
    }

    /**
     * Get support team member by ID
     * 
     * @urlParam id int required Support team member ID. Example: 1
     * 
     * @response 200 {"id": 1, "name": "John Doe", "phone": "0123456789", "zalo": "...", "avatar": "http://...", "role": "consultant", "group": "general", "position": 0, "status": 1, "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Support Team with ID [1] was not found."}
     * 
     * @throws NotFoundException
     */
    public function show(int $id): SupportTeamResource
    {
        return new SupportTeamResource($this->supportTeamService->show($id));
    }

    /**
     * Create new support team member
     * 
     * @bodyParam name string required Member name. Example: "John Doe"
     * @bodyParam phone string required Phone number (max 20). Example: "0123456789"
     * @bodyParam zalo string nullable Zalo contact. Example: "zalo123"
     * @bodyParam role string required Role (consultant/manager). Example: "consultant"
     * @bodyParam group string required Group (general/sales/technical). Example: "general"
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (0 or 1). Example: 1
     * @bodyParam avatar file nullable Member avatar
     * 
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->supportTeamService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * Update support team member
     * 
     * @urlParam id int required Support team member ID. Example: 1
     * @bodyParam name string required Member name. Example: "John Doe"
     * @bodyParam phone string required Phone number (max 20). Example: "0123456789"
     * @bodyParam zalo string nullable Zalo contact. Example: "zalo123"
     * @bodyParam role string required Role (consultant/manager). Example: "consultant"
     * @bodyParam group string required Group (general/sales/technical). Example: "general"
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (0 or 1). Example: 1
     * @bodyParam avatar file nullable Member avatar
     * 
     * @response 200 {"id": 1, "name": "John Doe", "phone": "0123456789", "zalo": "...", "avatar": "http://...", "role": "consultant", "group": "general", "position": 0, "status": 1, "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Support Team with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     * 
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): SupportTeamResource
    {
        return new SupportTeamResource($this->supportTeamService->update($id, $request->validated()));
    }

    /**
     * Delete support team member
     * 
     * @urlParam id int required Support team member ID. Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Support Team with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->supportTeamService->destroy($id);
        return new SuccessResponse([]);
    }
}

