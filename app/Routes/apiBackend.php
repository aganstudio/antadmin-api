<?php

use Illuminate\Support\Facades\Route;

$apiBackPrefix = config('app.api_backend_prefix');
//无需授权路由
Route::prefix('v2/' . $apiBackPrefix)->group(function () {
    Route::match(['post'], '/login', \App\ApiBackend\Login::class);
    Route::match(['post'], '/logout', \App\ApiBackend\Logout::class);
});
// 授权路由
Route::middleware('auth:sanctum')->prefix('v2/' . $apiBackPrefix)->group(function () {
    //登录用户
    Route::match(['post'], '/info', \App\ApiBackend\Admin\Info::class);
    Route::match(['post'], '/menulist', \App\ApiBackend\Admin\MenuPermission::class);
    // 管理用户
    Route::match(['post'], '/admin/page', \App\ApiBackend\Admin\Query::class);
    Route::match(['post'], '/admin/add', \App\ApiBackend\Admin\AddUpdate::class);
    Route::match(['post'], '/admin/delete', \App\ApiBackend\Admin\Delete::class);
    Route::match(['post'], '/admin/password', \App\ApiBackend\Admin\Password::class);
    Route::match(['post'], '/admin/status', \App\ApiBackend\Admin\Status::class);
    // 部门
    Route::match(['post'], '/dept/list', \App\ApiBackend\Department\Query::class);
    Route::match(['post'], '/dept/add', \App\ApiBackend\Department\AddUpdate::class);
    Route::match(['post'], '/dept/delete', \App\ApiBackend\Department\Delete::class);
    // 菜单
    Route::match(['post'], '/menu/list', \App\ApiBackend\Menu\Query::class);
    Route::match(['post'], '/menu/selectlist', \App\ApiBackend\Menu\SelectList::class);
    Route::match(['post'], '/menu/add', \App\ApiBackend\Menu\AddUpdate::class);
    Route::match(['post'], '/menu/delete', \App\ApiBackend\Menu\Delete::class);
    // 角色
    Route::match(['post'], '/role/list', \App\ApiBackend\Role\Query::class);
    Route::match(['post'], '/role/menulist', \App\ApiBackend\Role\MenuQuery::class);
    Route::match(['post'], '/role/page', \App\ApiBackend\Role\Query::class);
    Route::match(['post'], '/role/simpleupdate', \App\ApiBackend\Role\SimpleUpdate::class);
    Route::match(['post'], '/role/add', \App\ApiBackend\Role\AddUpdate::class);
    Route::match(['post'], '/role/delete', \App\ApiBackend\Role\Delete::class);
});
