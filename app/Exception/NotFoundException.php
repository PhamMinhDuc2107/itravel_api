<?php

namespace App\Exception;

class NotFoundException extends BusinessException
{
    /**
     * @param string|null $resource Resource name (e.g., 'Category', 'User')
     * @param string|int|null $id Resource ID
     * @param string|null $field Custom field name for lookup
     * @param string|null $value Custom field value
     */
    public function __construct(
        ?string $resource = null,
        string|int|null $id = null,
        ?string $field = null,
        ?string $value = null
    ) {
        $message = $this->buildMessage($resource, $id, $field, $value);

        parent::__construct($message, 404);
    }

    /**
     * Build error message from lang files
     */
    private function buildMessage(
        ?string $resource,
        string|int|null $id,
        ?string $field,
        ?string $value
    ): string {
        if (!$resource) {
            return __('message.not_found.default');
        }

        if ($field && $value) {
            return __('message.not_found.with_field', [
                'resource' => $resource,
                'field' => $field,
                'value' => $value,
            ]);
        }

        if ($id) {
            return __('message.not_found.with_id', [
                'resource' => $resource,
                'id' => $id,
            ]);
        }

        return __('message.not_found.default');
    }
}
