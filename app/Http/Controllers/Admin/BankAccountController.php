<?php

namespace App\Http\Controllers\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\BankAccount\StoreRequest;
use App\Http\Requests\Admin\BankAccount\UpdateRequest;
use App\Http\Requests\Admin\BulkDestroyRequest;
use App\Http\Resources\Admin\BankAccount\BankAccountCollectionResource;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\BankAccountService;
use Illuminate\Http\Request;

/**
 * @group Bank Accounts
 * 
 * API endpoints for managing bank accounts.
 */
readonly class BankAccountController
{
    public function __construct(
        private BankAccountService $bankAccountService
    ) {
    }

    /**
     * Get list of bank accounts
     * 
     * @queryParam page int Page number. Example: 1
     * @queryParam per_page int Items per page. Example: 15
     * 
     * @response 200 {"data": [{"id": 1, "bank_name": "Vietcombank", ...}]}
     */
    public function index(Request $request): BankAccountCollectionResource
    {
        $filterableColumns = ['status'];
        $context = QueryContext::fromRequest($request, $filterableColumns);
        $searchFields = ['bank_name', 'account_number', 'account_holder'];
        return (new BankAccountCollectionResource($this->bankAccountService->list($context, $searchFields)));
    }

    /**
     * Get bank account by ID
     * 
     * @urlParam id int required Bank account ID. Example: 1
     * 
     * @response 200 {"id": 1, "bank_name": "Vietcombank", "account_number": "1234567890", "account_holder": "John Doe", "branch": "...", "logo": "http://...", "qr_code": "http://...", "position": 0, "status": 1, "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Bank Account with ID [1] was not found."}
     * 
     * @throws NotFoundException
     */
    public function show(int $id): BankAccountResource
    {
        return new BankAccountResource($this->bankAccountService->show($id));
    }

    /**
     * Create new bank account
     * 
     * @bodyParam bank_name string required Bank name. Example: "Vietcombank"
     * @bodyParam account_number string required Account number (max 50). Example: "1234567890"
     * @bodyParam account_holder string required Account holder name. Example: "John Doe"
     * @bodyParam branch string nullable Branch name. Example: "Ha Noi"
     * @bodyParam logo file nullable Bank logo
     * @bodyParam qr_code file nullable QR code image
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (0 or 1). Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 422 {"message": "Validation error", "errors": {...}}
     */
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->bankAccountService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * Update bank account
     * 
     * @urlParam id int required Bank account ID. Example: 1
     * @bodyParam bank_name string required Bank name. Example: "Vietcombank"
     * @bodyParam account_number string required Account number (max 50). Example: "1234567890"
     * @bodyParam account_holder string required Account holder name. Example: "John Doe"
     * @bodyParam branch string nullable Branch name. Example: "Ha Noi"
     * @bodyParam logo file nullable Bank logo
     * @bodyParam qr_code file nullable QR code image
     * @bodyParam position int nullable Position (min 0). Example: 0
     * @bodyParam status int required Status (0 or 1). Example: 1
     * 
     * @response 200 {"id": 1, "bank_name": "Vietcombank", "account_number": "1234567890", "account_holder": "John Doe", "branch": "...", "logo": "http://...", "qr_code": "http://...", "position": 0, "status": 1, "created_at": "2024-01-01 00:00:00", "updated_at": "2024-01-01 00:00:00"}
     * @response 404 {"message": "The requested Bank Account with ID [1] was not found."}
     * @response 422 {"message": "Validation error", "errors": {...}}
     * 
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): BankAccountResource
    {
        return new BankAccountResource($this->bankAccountService->update($id, $request->validated()));
    }

    /**
     * Delete bank account
     * 
     * @urlParam id int required Bank account ID. Example: 1
     * 
     * @response 200 {"message": "Success"}
     * @response 404 {"message": "The requested Bank Account with ID [1] was not found."}
     */
    public function destroy(int $id): SuccessResponse
    {
        $this->bankAccountService->destroy($id);
        return new SuccessResponse([]);
    }

    /**
     * Bulk delete bank accounts
     *
     * Delete multiple bank accounts at once by providing an array of IDs.
     *
     * @bodyParam ids int[] required Array of bank account IDs to delete. Example: [1, 2, 3]
     *
     * @response 200 {"message": "Success", "data": {"deleted_count": 3}}
     * @response 422 {"message": "Validation error", "errors": {"ids": ["The ids field is required."]}}
     */
    public function bulkDestroy(BulkDestroyRequest $request): SuccessResponse
    {
        $deleted = $this->bankAccountService->destroyMultiple($request->validated('ids'));
        return new SuccessResponse(['deleted_count' => $deleted]);
    }
}

