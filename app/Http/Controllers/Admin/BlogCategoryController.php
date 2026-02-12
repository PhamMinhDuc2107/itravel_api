<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\BlogCategory\StoreRequest;
use App\Http\Requests\Admin\BlogCategory\UpdateRequest;
use App\Http\Requests\Admin\BulkDestroyRequest;
use App\Http\Resources\Admin\BlogCategory\BlogCategoryCollectionResource;
use App\Http\Resources\Admin\BlogCategory\BlogCategoryResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\BlogCategoryService;
use Illuminate\Http\Request;

/**
 * @group Blog Categories
 * 
 * API endpoints for managing blog categories.
 */
readonly class BlogCategoryController
{
    public function __construct(
        private BlogCategoryService $blogCategoryService
    ) {
    }

    /**
     * Get list of blog categories
     * 
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam q string Search term. Example: "Travel"
     * @queryParam search_by string Search by column (name, slug, description). Example: "name"
     * @queryParam status string Filter by status (active/inactive). Example: "active"
     * @queryParam sort string Sort field. Example: "name"
     * @queryParam order string Sort direction (asc/desc). Example: "asc"
     * 
     * @response 200 {"data": [{"id": 1, "name": "Travel", ...}]}
     */
    public function index(Request $request): BlogCategoryCollectionResource
    {
        $filterableColumns = ['status'];
        $context = QueryContext::fromRequest($request, $filterableColumns);
        
        $searchFields = ['name', 'slug', 'description'];
        return (new BlogCategoryCollectionResource($this->blogCategoryService->list($context, $searchFields)));
    }

    /**
     * Get blog category by ID
     * 
     * @urlParam id int required Blog category ID. Example: 1
     * 
     * @response 200 {"id": 1, "name": "Travel", "slug": "travel", "description": "...", "position": 0, "status": "active", "meta_title": "...", "meta_description": "...", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Blog Category with ID [1] was not found."}
     * 
     * @throws NotFoundException
     */
    public function show(int $id): BlogCategoryResource
    {
        return new BlogCategoryResource($this->blogCategoryService->show($id));
    }

    /**
     * Create new blog category
     * 
     * @bodyParam name string required Category name. Example: "Travel"
     * @bodyParam slug string required Unique slug. Example: "travel"
     * @bodyParam description string nullable Category description.
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status string required Status (active/inactive). Example: "active"
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     * 
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->blogCategoryService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * Update blog category
     * 
     * @urlParam id int required Blog category ID. Example: 1
     * @bodyParam name string required Category name. Example: "Travel"
     * @bodyParam slug string required Unique slug. Example: "travel"
     * @bodyParam description string nullable Category description.
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status string required Status (active/inactive). Example: "active"
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     * 
     * @response 200 {"id": 1, "name": "Travel", "slug": "travel", "description": "...", "position": 0, "status": "active", "meta_title": "...", "meta_description": "...", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Blog Category with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     * 
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): BlogCategoryResource
    {
        return new BlogCategoryResource($this->blogCategoryService->update($id, $request->validated()));
    }

    /**
     * Delete blog category
     * 
     * @urlParam id int required Blog category ID. Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Blog Category with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->blogCategoryService->destroy($id);
        return new SuccessResponse([]);
    }

    /**
     * Bulk delete blog categories
     *
     * Delete multiple blog categories at once by providing an array of IDs.
     *
     * @bodyParam ids int[] required Array of blog category IDs to delete. Example: [1, 2, 3]
     *
     * @response 200 {"message": "Success", "data": {"deleted_count": 3}}
     * @response 422 {"message": "Validation error", "errors": {"ids": ["The ids field is required."]}}
     */
    public function bulkDestroy(BulkDestroyRequest $request): SuccessResponse
    {
        $deleted = $this->blogCategoryService->destroyMultiple($request->validated('ids'));
        return new SuccessResponse(['deleted_count' => $deleted]);
    }
}

