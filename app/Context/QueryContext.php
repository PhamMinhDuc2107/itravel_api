<?php

namespace App\Context;

use Illuminate\Http\Request;

readonly class QueryContext
{
    public function __construct(
        public ?string $q,
        public ?string $searchBy,
        public string  $sort,
        public string  $order,
        public int     $limit,
        public array   $params
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->query('q'),
            $request->query('search_by'),
            $request->query('sort', 'id'),
            $request->query('order', 'desc'),
            (int) $request->query('limit', 10),
            $request->all()
        );
    }

    public function toArray(): array
    {
        return array_merge($this->params, [
            'q' => $this->q,
            'search_by' => $this->searchBy,
            'sort' => $this->sort,
            'order' => $this->order,
            'limit' => $this->limit,
        ]);
    }
}
