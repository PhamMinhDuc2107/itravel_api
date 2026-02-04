<?php

namespace App\Http\Responses;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Enum\AppErrorEnum;

class SuccessResponse extends BaseApiResponse
{
    /**
     * @param mixed $data (Array, Object, Paginator, hoặc null)
     * @param string|null $message Message (Ex: "Created")
     * @param int $statusCode (200, 201, 204)
     */
    public function __construct(mixed $data = [], ?string $message = null, int $statusCode = 200)
    {
        parent::__construct(AppErrorEnum::SUCCESS, $statusCode);
        $this->message = $message;

        if ($data instanceof LengthAwarePaginator) {
            $this->data = $data->items();
            $this->pagination = [
                'total'        => $data->total(),
                'per_page'     => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
            ];
        } else {
            $this->data = $data;
        }
    }
}
