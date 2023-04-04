<?php

use Illuminate\Support\Facades\Route;

$apiBackPrefix = config('app.api_backend_prefix');
//后端路由
Route::middleware('auth:sanctum')->prefix("v1/" . $apiBackPrefix . '/demo')->group(function () {
    Route::get('/test', function () {
        return "demo apiBackend test...";
    });
});

//前端路由
Route::prefix('/v1/demo')->group(function () {
    Route::get('/test', function () {
        return "demo apiFrontend test...";
    });
});
