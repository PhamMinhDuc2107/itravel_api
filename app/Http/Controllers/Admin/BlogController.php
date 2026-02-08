<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Blog\StoreRequest;
use App\Http\Requests\Admin\Blog\UpdateRequest;
use App\Http\Resources\Admin\Blog\BlogCollectionResource;
use App\Http\Resources\Admin\Blog\BlogResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\BlogService;
use Illuminate\Http\Request;

/**
 * @group Blogs
 * 
 * API endpoints for managing blog posts.
 */
readonly class BlogController
{
    public function __construct(
        private BlogService $blogService
    ) {}

    /**
     * Get list of blogs
     * 
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * @queryParam search string Search term. Example: "travel"
     * @queryParam sort string Sort field. Example: "created_at"
     * @queryParam order string Sort direction (asc/desc). Example: "desc"
     * 
     * @response 200 {"data": [{"id": 1, "name": "Blog Title", ...}]}
     */
    public function index(Request $request): BlogCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new BlogCollectionResource($this->blogService->list($context)));
    }

    /**
     * Get blog by ID
     * 
     * @urlParam id int required Blog ID. Example: 1
     * 
     * @response 200 {"id": 1, "name": "Blog Title", "slug": "blog-title", "excerpt": "...", "content": "...", "image": "http://...", "category_id": 1, "author_id": 1, "status": "published", "is_featured": 0, "view_count": 0, "published_at": "2024-01-01 00:00:00", "meta_title": "...", "meta_description": "...", "meta_keywords": "...", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Blog with ID [1] was not found."}
     * 
     * @throws NotFoundException
     */
    public function show(int $id): BlogResource
    {
        return new BlogResource($this->blogService->show($id));
    }

    /**
     * Create new blog
     * 
     * @bodyParam name string required Blog title. Example: "Travel Guide"
     * @bodyParam slug string required Unique slug. Example: "travel-guide"
     * @bodyParam excerpt string nullable Blog excerpt.
     * @bodyParam content string required Blog content.
     * @bodyParam image file nullable Blog featured image
     * @bodyParam category_id int nullable Blog category ID. Example: 1
     * @bodyParam author_id int required Author (admin) ID. Example: 1
     * @bodyParam status string required Status (draft/published/archived). Example: "published"
     * @bodyParam is_featured int nullable Is featured (0 or 1). Example: 0
     * @bodyParam published_at date nullable Published date. Example: "2024-01-01"
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     * @bodyParam meta_keywords string nullable Meta keywords (max 255).
     * 
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->blogService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * Update blog
     * 
     * @urlParam id int required Blog ID. Example: 1
     * @bodyParam name string required Blog title. Example: "Travel Guide"
     * @bodyParam slug string required Unique slug. Example: "travel-guide"
     * @bodyParam excerpt string nullable Blog excerpt.
     * @bodyParam content string required Blog content.
     * @bodyParam image file nullable Blog featured image
     * @bodyParam category_id int nullable Blog category ID. Example: 1
     * @bodyParam author_id int required Author ID. Example: 1
     * @bodyParam status string required Status (draft/published/archived). Example: "published"
     * @bodyParam is_featured int nullable Is featured (0 or 1). Example: 0
     * @bodyParam published_at date nullable Published date. Example: "2024-01-01"
     * @bodyParam meta_title string nullable Meta title (max 255).
     * @bodyParam meta_description string nullable Meta description (max 500).
     * @bodyParam meta_keywords string nullable Meta keywords (max 255).
     * 
     * @response 200 {"id": 1, "name": "Blog Title", "slug": "blog-title", "excerpt": "...", "content": "...", "image": "http://...", "category_id": 1, "author_id": 1, "status": "published", "is_featured": 0, "view_count": 0, "published_at": "2024-01-01 00:00:00", "meta_title": "...", "meta_description": "...", "meta_keywords": "...", "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Blog with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     * 
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): BlogResource
    {
        return new BlogResource($this->blogService->update($id, $request->validated()));
    }

    /**
     * Delete blog
     * 
     * @urlParam id int required Blog ID. Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Blog with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->blogService->destroy($id);
        return new SuccessResponse([]);
    }
}

