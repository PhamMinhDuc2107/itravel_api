<?php
namespace App\Http\Routes;

use Illuminate\Support\Facades\Route;

class RouteRegistry
{
    public static function api(): void
    {
        Route::middleware('api')
            ->prefix('admin')
            ->group(base_path('routes/admin.php'));

        Route::middleware('api')
            ->prefix('client')
            ->group(base_path('routes/client.php'));
    }

    public static function web(): void
    {
        // Register router web
    }

    public static function console(): void
    {
        // Register router console
    }


}
