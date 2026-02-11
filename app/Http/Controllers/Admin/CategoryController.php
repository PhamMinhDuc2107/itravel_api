<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Category\StoreRequest;
use App\Http\Requests\Admin\Category\UpdateRequest;
use App\Http\Requests\Admin\BulkDestroyRequest;
use App\Http\Resources\Admin\Category\CategoryCollectionResource;
use App\Http\Resources\Admin\Category\CategoryResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\CategoryService;
use Illuminate\Http\Request;

/**
 * @group Categories
 * 
 * API endpoints for managing tour categories.
 */
readonly class CategoryController
{
    public function __construct(
        private CategoryService $categoryService
    ) {
    }

    /**
     * Get list of categories
     * 
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam q string Search term (searches: name, slug, description). Example: "travel"
     * @queryParam search_by string Specific field to search. Example: "name"
     * @queryParam sort string Sort field. Example: "name"
     * @queryParam order string Sort direction (asc/desc). Example: "asc"
     * @queryParam status int Filter by status (0 or 1). Example: 1
     * @queryParam parent_id int Filter by parent category ID. Example: 1
     * 
     * @response 200 {"data": [{"id": 1, "name": "Travel", "slug": "travel", ...}]}
     */
    public function index(Request $request): CategoryCollectionResource
    {
        // Define filterable columns for this endpoint
        $filterableColumns = ['status', 'parent_id'];

        $context = QueryContext::fromRequest($request, $filterableColumns);

        return (new CategoryCollectionResource($this->categoryService->list($context)));
    }

    /**
     * Get category by ID
     * 
     * @urlParam id int required Category ID. Example: 1
     * 
     * @response 200 {"id": 1, "name": "Travel", "slug": "travel", "parent_id": null, "description": "...", "position": 0, "status": 1, "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Category with ID [1] was not found."}
     * 
     * @throws NotFoundException
     */
    public function show(int $id): CategoryResource
    {
        return new CategoryResource($this->categoryService->show($id));
    }

    /**
     * Create new category
     * 
     * @bodyParam name string required Category name. Example: "Travel"
     * @bodyParam slug string required Unique slug. Example: "travel"
     * @bodyParam parent_id int nullable Parent category ID. Example: null
     * @bodyParam description string nullable Category description.
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (0 or 1). Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->categoryService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * Update category
     * 
     * @urlParam id int required Category ID. Example: 1
     * @bodyParam name string required Category name. Example: "Travel"
     * @bodyParam slug string required Unique slug. Example: "travel"
     * @bodyParam parent_id int nullable Parent category ID (cannot be itself). Example: null
     * @bodyParam description string nullable Category description.
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (0 or 1). Example: 1
     * 
     * @response 200 {"id": 1, "name": "Travel", "slug": "travel", ...}
     * @response 404 {"message": "The requested Category with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     * 
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): CategoryResource
    {
        return new CategoryResource($this->categoryService->update($id, $request->validated()));
    }

    /**
     * Delete category
     * 
     * @urlParam id int required Category ID. Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Category with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->categoryService->destroy($id);
        return new SuccessResponse([]);
    }

    /**
     * Bulk delete categories
     *
     * Delete multiple categories at once by providing an array of IDs.
     *
     * @bodyParam ids int[] required Array of category IDs to delete. Example: [1, 2, 3]
     *
     * @response 200 {"message": "Success", "data": {"deleted_count": 3}}
     * @response 422 {"message": "Validation error", "errors": {"ids": ["The ids field is required."]}}
     */
    public function bulkDestroy(BulkDestroyRequest $request): SuccessResponse
    {
        $deleted = $this->categoryService->destroyMultiple($request->validated('ids'));
        return new SuccessResponse(['deleted_count' => $deleted]);
    }
}

