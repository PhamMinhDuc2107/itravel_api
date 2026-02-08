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

readonly class SupportTeamController
{
    public function __construct(
        private SupportTeamService $supportTeamService
    ) {}

    public function index(Request $request): SupportTeamCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new SupportTeamCollectionResource($this->supportTeamService->list($context)));
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): SupportTeamResource
    {
        return new SupportTeamResource($this->supportTeamService->show($id));
    }

    public function store(StoreRequest $request): SuccessResponse
    {
        $this->supportTeamService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): SupportTeamResource
    {
        return new SupportTeamResource($this->supportTeamService->update($id, $request->validated()));
    }

    public function destroy(int $id): SuccessResponse
    {
        $this->supportTeamService->destroy($id);
        return new SuccessResponse([]);
    }
}

