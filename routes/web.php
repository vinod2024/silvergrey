<?php

use App\Http\Controllers\ProfileController;

use App\Http\Middleware\SubdomainSetup;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyProfileController;

// use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/* Route::domain('{subdomain}.localhost')->group(function () {
    Route::get('/dashboard', [CompanyProfileController::class, 'index'])->name('dashboard');
    // Other company-specific routes
}); */



// Route::domain('{domain_name}.localhost::8000')->group(function () {
Route::group(['domain' => '{account}.localhost'], function () {
    // Route::get('/dashboard', function ($domain_name) {
    // echo $account; exit;
    
    /* Route::get('/home', function ($account) {
        dd($account);
    }); */

    Route::get('/home', [CompanyProfileController::class, 'home']);

    // return 'hi';
    // })->name('dashboard');
});

Route::get('/dashboard', [CompanyProfileController::class, 'index'])->middleware(['auth'])->name('dashboard');
// Route::get('/test', [CompanyProfileController::class, 'test'])->middleware(['auth', 'role:admin'])->name('dashboard.test');

/* Route::group(['middleware' => ['subdomain_setup']], function(){
    // Route::get('/home', [CompanyProfileController::class, 'home'])->name('dashboard');
    Route::get('/dashboard', [CompanyProfileController::class, 'index'])->name('dashboard');
}); */


/* Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); */

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::group(['middleware' => ['subdomain_setup']], function(){
    require __DIR__.'/auth.php';
// });
