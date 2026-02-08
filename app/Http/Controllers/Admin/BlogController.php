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

readonly class BlogController
{
    public function __construct(
        private BlogService $blogService
    ) {}

    public function index(Request $request): BlogCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new BlogCollectionResource($this->blogService->list($context)));
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): BlogResource
    {
        return new BlogResource($this->blogService->show($id));
    }

    public function store(StoreRequest $request): SuccessResponse
    {
        $this->blogService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): BlogResource
    {
        return new BlogResource($this->blogService->update($id, $request->validated()));
    }

    public function destroy(int $id): SuccessResponse
    {
        $this->blogService->destroy($id);
        return new SuccessResponse([]);
    }
}

