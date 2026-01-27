<?php

use App\Http\Responses\SuccessResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return new SuccessResponse(['1' => "oke"]);
});
