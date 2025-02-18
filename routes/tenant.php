<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRUDController;
use App\Http\Controllers\Tenant\UsersController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\Tenant\Auth\AuthController;
use App\Http\Controllers\Tenant\DashboardController;


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


Route::middleware(['auth',  'CheckSubscription'])->group(function () {

    Route::get('/test', [DashboardController::class, 'test']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    Route::get('seeding_database/{skip}/{limit}', [DashboardController::class, 'seeding_database']);

    Route::get('change-language/{lang}', [DashboardController::class, 'changeLanguage'])->name('dashboard.change-language');
    Route::get('change-mode/{mode}', [DashboardController::class, 'changeMode'])->name('dashboard.change-mode');


    # Users
    Route::get('users-for-select/{roles?}', [UsersController::class, 'usersForSelect'])->name('users.select');
    Route::get('users/datatable', [UsersController::class, 'datatable']);
    Route::get('users/quick-add', [UsersController::class, 'quickAdd'])->name('users.quick-add');
    Route::resource('users', UsersController::class);

    Route::get('items-for-select/{data}', [CRUDController::class, 'itemsForSelect'])->name('items.select')->withoutMiddleware('login.post-checks');
});

Route::get('contacts-password-reset/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
//Route::get('/', [DashboardController::class, 'shop']);
Route::get('/', [AuthController::class, 'loginPage'])->name('login')->middleware(RedirectIfAuthenticated::class);
Route::get('login', [AuthController::class, 'loginPage'])->name('login')->middleware(RedirectIfAuthenticated::class);
Route::post('login', [AuthController::class, 'login'])->name('login.post');


Route::get('logout', [AuthController::class, 'logout'])->name('logout');

# Load Platform Alert Messages
Route::get('impersonate/{token}', [AuthController::class, 'impersonate'])->name('impersonate');

