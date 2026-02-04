<?php

namespace App\Enum;

use Illuminate\Http\Response;

enum AppErrorEnum: string
{
    case SUCCESS            = 'SUCCESS';
    case SERVER_ERROR       = 'SERVER_ERROR';
    case BAD_REQUEST        = 'BAD_REQUEST';
    case UNAUTHORIZED       = 'UNAUTHORIZED';
    case FORBIDDEN          = 'FORBIDDEN';
    case NOT_FOUND          = 'NOT_FOUND';
    case VALIDATION_ERROR   = 'VALIDATION_ERROR';
    case DATA_EXISTS        = 'DATA_EXISTS';
    case ACCOUNT_LOCKED     = 'ACCOUNT_LOCKED';
    case TOUR_SOLD_OUT      = 'TOUR_SOLD_OUT';

    /**
     * Get HTTP Status Code
     */
    public function httpStatus(): int
    {
        return match ($this) {
            self::SUCCESS           => Response::HTTP_OK,                    // 200
            self::SERVER_ERROR      => Response::HTTP_INTERNAL_SERVER_ERROR, // 500
            self::BAD_REQUEST,
            self::DATA_EXISTS,
            self::TOUR_SOLD_OUT     => Response::HTTP_BAD_REQUEST,           // 400
            self::UNAUTHORIZED,
            self::ACCOUNT_LOCKED    => Response::HTTP_UNAUTHORIZED,          // 401
            self::FORBIDDEN         => Response::HTTP_FORBIDDEN,             // 403
            self::NOT_FOUND         => Response::HTTP_NOT_FOUND,             // 404
            self::VALIDATION_ERROR  => Response::HTTP_UNPROCESSABLE_ENTITY,  // 422
        };
    }

    /**
     * Get Message
     */
    public function message(): string
    {
        return match ($this) {
            self::SUCCESS           => 'Action completed successfully.',
            self::SERVER_ERROR      => 'Internal server error. Please try again later.',
            self::BAD_REQUEST       => 'Invalid request.',
            self::UNAUTHORIZED      => 'Unauthorized access. Please login.',
            self::FORBIDDEN         => 'You do not have permission to perform this action.',
            self::NOT_FOUND         => 'The requested resource was not found.',
            self::VALIDATION_ERROR  => 'The given data was invalid.',
            self::DATA_EXISTS       => 'Data already exists in the system.',
            self::ACCOUNT_LOCKED    => 'Your account has been locked.',
            self::TOUR_SOLD_OUT     => 'This tour is sold out.',
        };
    }
}
