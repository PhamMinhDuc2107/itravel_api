<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use App\Context\AppContext;
use App\Enums\AppErrorEnum;

abstract class BaseApiResponse implements Responsable
{
    protected mixed $data = [];
    protected AppErrorEnum $statusEnum;
    protected int $statusCode;
    protected array $pagination = [];
    protected ?string $message = null;

    public function __construct(
        AppErrorEnum $statusEnum,
        int $statusCode,
    ) {
        $this->statusEnum = $statusEnum;
        $this->statusCode = $statusCode;
    }

    public function toResponse($request): JsonResponse
    {
        $payload = [
            'data' => $this->data,
            'meta' => [
                'timestamp'   => now()->toIso8601String(),
                'request_id'  => AppContext::getRequestId(),
                'version'     => config('app.version', '1.0.0'),
            ]
        ];

        if ($this->message) {
            $payload['meta']['message'] = $this->message;
        }

        if (!empty($this->pagination)) {
            $payload['meta']['pagination'] = $this->pagination;
        }

        return response()->json($payload, $this->statusCode);
    }
}
