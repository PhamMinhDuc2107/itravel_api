<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Banner\StoreRequest;
use App\Http\Requests\Admin\Banner\UpdateRequest;
use App\Http\Resources\Admin\Banner\BannerCollectionResource;
use App\Http\Resources\Admin\Banner\BannerResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\BannerService;
use Illuminate\Http\Request;

readonly class BannerController
{
    public function __construct(
        private BannerService $bannerService
    ) {}

    public function index(Request $request): BannerCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new BannerCollectionResource($this->bannerService->list($context)));
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): BannerResource
    {
        return new BannerResource($this->bannerService->show($id));
    }

    public function store(StoreRequest $request): SuccessResponse
    {
        $this->bannerService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): BannerResource
    {
        return new BannerResource($this->bannerService->update($id, $request->validated()));
    }

    public function destroy(int $id): SuccessResponse
    {
        $this->bannerService->destroy($id);
        return new SuccessResponse([]);
    }
}

