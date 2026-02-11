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
        public int     $page,
        public array   $params,
        public FilterContext $filters
    ) {}

    /**
     * Create QueryContext from Request
     *
     * @param Request $request
     * @param array $filterableColumns Allowed filterable columns (e.g., ['status', 'payment_status', 'is_featured'])
     */
    public static function fromRequest(Request $request, array $filterableColumns = []): self
    {
        $params = $request->all();

        // Extract filters from request
        $filters = FilterContext::fromRequest($params, $filterableColumns);

        return new self(
            $request->query('q') ?: $request->query('search'),
            $request->query('search_by'),
            $request->query('sort', 'id'),
            $request->query('order', 'desc'),
            (int) $request->query('per_page', $request->query('limit', 15)),
            (int) $request->query('page', 1),
            $params,
            $filters
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
            'page' => $this->page,
            'filters' => $this->filters->toArray(),
        ]);
    }

    /**
     * Check if has any filters
     */
    public function hasFilters(): bool
    {
        return !$this->filters->isEmpty();
    }
}

