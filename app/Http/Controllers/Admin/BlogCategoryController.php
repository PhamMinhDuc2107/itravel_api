<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\BlogCategory\StoreRequest;
use App\Http\Requests\Admin\BlogCategory\UpdateRequest;
use App\Http\Resources\Admin\BlogCategory\BlogCategoryCollectionResource;
use App\Http\Resources\Admin\BlogCategory\BlogCategoryResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\BlogCategoryService;
use Illuminate\Http\Request;

readonly class BlogCategoryController
{
    public function __construct(
        private BlogCategoryService $blogCategoryService
    ) {}

    public function index(Request $request): BlogCategoryCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new BlogCategoryCollectionResource($this->blogCategoryService->list($context)));
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): BlogCategoryResource
    {
        return new BlogCategoryResource($this->blogCategoryService->show($id));
    }

    public function store(StoreRequest $request): SuccessResponse
    {
        $this->blogCategoryService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): BlogCategoryResource
    {
        return new BlogCategoryResource($this->blogCategoryService->update($id, $request->validated()));
    }

    public function destroy(int $id): SuccessResponse
    {
        $this->blogCategoryService->destroy($id);
        return new SuccessResponse([]);
    }
}

