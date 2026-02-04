<?php

namespace App\Exception;

use App\Enum\AppErrorEnum;

class NotFoundException extends BusinessException
{
    /**
     * @param string|null $resource
     * @param string|int|null $id
     */
    public function __construct(?string $resource = null, string|int|null $id = null)
    {
        $message = null;

        if ($resource) {
            $message = "The requested {$resource}";

            if ($id) {
                $message .= " with ID [{$id}]";
            }

            $message .= " was not found.";
        }


        parent::__construct(
            errorEnum: AppErrorEnum::NOT_FOUND,
            customMessage: $message
        );
    }
}
