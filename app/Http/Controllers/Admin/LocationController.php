<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Location\StoreRequest;
use App\Http\Requests\Admin\Location\UpdateRequest;
use App\Http\Resources\Admin\Location\LocationCollectionResource;
use App\Http\Resources\Admin\Location\LocationResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\LocationService;
use Illuminate\Http\Request;

readonly class LocationController
{
    public function __construct(
        private LocationService $locationService
    ) {}

    public function index(Request $request): LocationCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new LocationCollectionResource($this->locationService->list($context)));
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): LocationResource
    {
        return new LocationResource($this->locationService->show($id));
    }

    public function store(StoreRequest $request): SuccessResponse
    {
        $this->locationService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): LocationResource
    {
        return new LocationResource($this->locationService->update($id, $request->validated()));
    }

    public function destroy(int $id): SuccessResponse
    {
        $this->locationService->destroy($id);
        return new SuccessResponse([]);
    }
}

