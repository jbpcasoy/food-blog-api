<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });
});


Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('/api')->group(function () {
    Route::post('/login', [AuthController::class, "login"]);

    Route::get('/posts', [PostController::class, "index"]);
    Route::get('/posts/{id}', [PostController::class, "show"]);


    Route::group(["middleware" => ["auth:sanctum"]], function () {
        Route::get('/user', [AuthController::class, "user"]);
        Route::post('/posts', [PostController::class, "store"]);
        Route::put('/posts/{id}', [PostController::class, "update"]);
        Route::delete('/posts/{id}', [PostController::class, "destroy"]);
    });
});
