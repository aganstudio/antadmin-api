<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            //后端
            Route::middleware('api')->group(base_path('app/routes/apiBackend.php'));
            //前端
            Route::middleware('api')->group(base_path('app/routes/apiFrontend.php'));

            //模块路由
            if (config('module.enabled')) {
                $modulePathArr = File::directories(base_path(config('module.path')));
                foreach ($modulePathArr as $modulePath) {
                    Route::middleware('api')->group($modulePath . '/route.php');
                }

            }
            //404
            Route::any('{any}', function () {
                return ['code' => 404, 'message' => '访问路径不存在', 'result' => []];
            })->where('any', '.*');
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        // 中间件<throttle:api> 速率限制
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
