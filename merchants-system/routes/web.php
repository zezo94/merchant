<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Home
    |--------------------------------------------------------------------------
    */
    Route::get('/', function () {
        return redirect()->route('merchants.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile/change-password', [ProfileController::class, 'editPassword'])
        ->name('profile.password.edit');

    Route::put('/profile/change-password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    /*
    |--------------------------------------------------------------------------
    | Logs
    |--------------------------------------------------------------------------
    | الحماية الفعلية داخل LogsController على is_root
    */
    Route::get('/logs', [LogsController::class, 'index'])
        ->name('logs.index');

    /*
    |--------------------------------------------------------------------------
    | Users Management
    |--------------------------------------------------------------------------
    | محمية بـ manage users
    | root يتجاوز كل شيء عبر Gate::before
    */
    Route::get('/users', [UserManagementController::class, 'index'])
        ->middleware('permission:manage users')
        ->name('users.index');

    Route::get('/users/create', [UserManagementController::class, 'create'])
        ->middleware('permission:manage users')
        ->name('users.create');

    Route::post('/users', [UserManagementController::class, 'store'])
        ->middleware('permission:manage users')
        ->name('users.store');

    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->middleware('permission:manage users')
        ->name('users.edit');

    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->middleware('permission:manage users')
        ->name('users.update');

    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
        ->middleware('permission:manage users')
        ->name('users.destroy');

    Route::get('/users/{user}/reset-password', [UserManagementController::class, 'showResetPasswordForm'])
        ->middleware('permission:manage users')
        ->name('users.reset-password.form');

    Route::put('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])
        ->middleware('permission:manage users')
        ->name('users.reset-password');

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Merchants - Print / Export / Inline Update
    |--------------------------------------------------------------------------
    */
    Route::get('/merchants/print', [MerchantController::class, 'print'])
        ->middleware('permission:print merchants')
        ->name('merchants.print');

    Route::get('/merchants/export', [MerchantController::class, 'export'])
        ->middleware('permission:export merchants')
        ->name('merchants.export');

    Route::patch('/merchants/{merchant}/inline-update', [MerchantController::class, 'inlineUpdate'])
        ->middleware('permission:edit merchants')
        ->name('merchants.inlineUpdate');

    /*
    |--------------------------------------------------------------------------
    | Merchants CRUD
    |--------------------------------------------------------------------------
    */
    Route::get('/merchants', [MerchantController::class, 'index'])
        ->middleware('permission:view merchants')
        ->name('merchants.index');

    Route::get('/merchants/create', [MerchantController::class, 'create'])
        ->middleware('permission:create merchants')
        ->name('merchants.create');

    Route::post('/merchants', [MerchantController::class, 'store'])
        ->middleware('permission:create merchants')
        ->name('merchants.store');

    Route::get('/merchants/{merchant}', [MerchantController::class, 'show'])
        ->middleware('permission:view merchants')
        ->name('merchants.show');

    Route::get('/merchants/{merchant}/edit', [MerchantController::class, 'edit'])
        ->middleware('permission:edit merchants')
        ->name('merchants.edit');

    Route::put('/merchants/{merchant}', [MerchantController::class, 'update'])
        ->middleware('permission:edit merchants')
        ->name('merchants.update');

    Route::delete('/merchants/{merchant}', [MerchantController::class, 'destroy'])
        ->middleware('permission:delete merchants')
        ->name('merchants.destroy');

    Route::get('/merchants/{merchant}/print-card', [MerchantController::class, 'printSingle'])
        ->name('merchants.show.print');

    Route::get('/merchants/{merchant}/export-card', [MerchantController::class, 'exportSingle'])
        ->name('merchants.show.export');


});
