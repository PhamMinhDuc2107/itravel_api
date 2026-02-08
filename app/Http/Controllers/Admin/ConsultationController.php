<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Consultation\StoreRequest;
use App\Http\Requests\Admin\Consultation\UpdateRequest;
use App\Http\Resources\Admin\Consultation\ConsultationCollectionResource;
use App\Http\Resources\Admin\Consultation\ConsultationResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\ConsultationService;
use Illuminate\Http\Request;

readonly class ConsultationController
{
    public function __construct(
        private ConsultationService $consultationService
    ) {}

    public function index(Request $request): ConsultationCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new ConsultationCollectionResource($this->consultationService->list($context)));
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): ConsultationResource
    {
        return new ConsultationResource($this->consultationService->show($id));
    }

    public function store(StoreRequest $request): SuccessResponse
    {
        $this->consultationService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): ConsultationResource
    {
        return new ConsultationResource($this->consultationService->update($id, $request->validated()));
    }

    public function destroy(int $id): SuccessResponse
    {
        $this->consultationService->destroy($id);
        return new SuccessResponse([]);
    }
}

