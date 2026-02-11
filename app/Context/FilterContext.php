<?php

namespace App\Context;

class FilterContext
{
    /**
     * @param array<string, mixed> $filters Key-value pairs of filters
     */
    public function __construct(
        private array $filters = []
    ) {}

    /**
     * Create from request query parameters
     */
    public static function fromRequest(array $params, array $allowedFilters = []): self
    {
        $filters = [];

        foreach ($allowedFilters as $key) {
            if (isset($params[$key]) && $params[$key] !== '' && $params[$key] !== null) {
                $filters[$key] = $params[$key];
            }
        }

        return new self($filters);
    }

    /**
     * Get all filters
     */
    public function all(): array
    {
        return $this->filters;
    }

    /**
     * Get specific filter value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->filters[$key] ?? $default;
    }

    /**
     * Check if filter exists
     */
    public function has(string $key): bool
    {
        return isset($this->filters[$key]);
    }

    /**
     * Check if any filters are set
     */
    public function isEmpty(): bool
    {
        return empty($this->filters);
    }

    /**
     * Get filters as array
     */
    public function toArray(): array
    {
        return $this->filters;
    }
}

