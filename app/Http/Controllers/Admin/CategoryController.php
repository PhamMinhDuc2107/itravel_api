<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Category\StoreRequest;
use App\Http\Requests\Admin\Category\UpdateRequest;
use App\Http\Resources\Admin\Category\CategoryCollectionResource;
use App\Http\Resources\Admin\Category\CategoryResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\CategoryService;
use Illuminate\Http\Request;

readonly class CategoryController
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(Request $request): CategoryCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new CategoryCollectionResource($this->categoryService->list($context)));
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): CategoryResource
    {
        return new CategoryResource($this->categoryService->show($id));
    }

    public function store(StoreRequest $request): SuccessResponse
    {
        $this->categoryService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): CategoryResource
    {
        return new CategoryResource($this->categoryService->update($id, $request->validated()));
    }

    public function destroy(int $id): SuccessResponse
    {
        $this->categoryService->destroy($id);
        return new SuccessResponse([]);
    }
}

