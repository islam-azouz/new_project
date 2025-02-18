<?php

use App\Http\Controllers\Central\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaasGate\SG_AccountsController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

# Saas Gate
Route::middleware(['CheckSaasGate'])->group(function () {
    Route::prefix('Saas-Gate')->group(function () {
        # Accounts_Controller
        Route::post('create-account', [SG_AccountsController::class, 'createAccount']);
        Route::post('update-account', [SG_AccountsController::class, 'updateAccount']);
        Route::post('delete-account', [SG_AccountsController::class, 'deleteAccount']);
        Route::post('update-account-subscription', [SG_AccountsController::class, 'updateAccountSubscription']);
        Route::post('update-account-subscription-addons', [SG_AccountsController::class, 'updateAccountSubscriptionAddons']);

    });
});

Route::get('signup/confirm/{accountId}', [AuthController::class, 'confirmPage'])->name('confirm');
Route::post('signup/confirm-account', [AuthController::class, 'confirmAccount'])->name('confirm-account');
Route::post('signup/resend-confirmation-code', [AuthController::class, 'resendConfirmationCode'])->name('resend-confirmation-code');
Route::post('signup/check-sub-domain', [AuthController::class, 'checkSubDomain'])->name('check-sub-domain');
Route::get('signup', [AuthController::class, 'signupPage'])->name('signup');
Route::post('signup', [AuthController::class, 'signup'])->name('signup.post');

Route::get('login', [AuthController::class, 'loginPage'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');


#TODO: Solve the route:cache artisan command call issue
