<?php
namespace App\Http\Controllers\Admin;
use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Http\Requests\Admin\Admin\StoreRequest;
use App\Http\Requests\Admin\Admin\UpdateRequest;
use App\Http\Resources\Admin\Admin\AdminCollectionResource;
use App\Http\Resources\Admin\Admin\AdminResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\AdminService;
use Illuminate\Http\Request;

readonly class AdminController
{
    public function __construct(
        private AdminService $adminService
    ) {}
    public function index(Request $request): AdminCollectionResource
    {
        $context = QueryContext::fromRequest($request);
        return (new AdminCollectionResource($this->adminService->list($context)));
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): AdminResource
    {
        return new AdminResource($this->adminService->show($id));
    }
    public function store(StoreRequest $request): SuccessResponse
    {
        $this->adminService->store($request->validated());
        return new SuccessResponse([]);
    }

    /**
     * @throws NotFoundException
     */
    public function update(UpdateRequest $request, int $id): AdminResource
    {
        return new AdminResource($this->adminService->update($id, $request->validated()));
    }
    public function destroy(int $id): SuccessResponse
    {
        $this->adminService->destroy($id);
        return new SuccessResponse([]);
    }
}
