<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\BankAccount\StoreRequest;
use App\Http\Requests\Admin\BankAccount\UpdateRequest;
use App\Http\Resources\Admin\BankAccount\BankAccountCollectionResource;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\BankAccountService;
use Illuminate\Http\Request;

readonly class BankAccountController
{
    public function __construct(
        private BankAccountService $bankAccountService
    ) {}

    public function index(Request $request): BankAccountCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new BankAccountCollectionResource($this->bankAccountService->list($context)));
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): BankAccountResource
    {
        return new BankAccountResource($this->bankAccountService->show($id));
    }

    public function store(StoreRequest $request): SuccessResponse
    {
        $this->bankAccountService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): BankAccountResource
    {
        return new BankAccountResource($this->bankAccountService->update($id, $request->validated()));
    }

    public function destroy(int $id): SuccessResponse
    {
        $this->bankAccountService->destroy($id);
        return new SuccessResponse([]);
    }
}

