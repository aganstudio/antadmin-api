<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//无需授权路由
Route::prefix('v1/')->group(function () {
});
// 授权路由
Route::middleware('auth:sanctum')->prefix('v1/')->group(function () {
});

//本地测试
Route::get('/test', function (Request $request) {
    return $request->ip();
});
