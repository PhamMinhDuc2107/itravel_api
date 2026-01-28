<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['api'])
    ->group(base_path('routes/admin.php'));
