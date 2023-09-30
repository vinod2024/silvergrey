<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Super admin login
Route::post('/super-login', [CompanyController::class, 'login']);

// Super admin create company.
Route::middleware(['auth:api', 'role:super_admin'])->group( function () {
    Route::post('/company-register', [CompanyController::class, 'store'])->name('users.create');
});

// Admin create user.
Route::middleware(['auth:api', 'role:admin'])->group( function () {
    Route::post('/create-user', [UserController::class, 'store']);
});

// User can login his database.
Route::middleware(['subdomain_setup'])->group( function () {
    Route::post('/user-login', [UserController::class, 'login']);
});
/* Route::middleware(['auth:api', 'role:user'])->group( function () {
    Route::post('/user-login', [UserController::class, 'login']);
}); */

/* Route::middleware(['auth:api', 'role:super_admin'])->group(function(){
    Route::get('/get-user', [UserController::class, 'userInfo']);
}); */


