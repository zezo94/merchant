<?php
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProfileController;
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

    Route::get('/profile/change-password', [ProfileController::class, 'editPassword'])
        ->name('profile.password.edit');

    Route::put('/profile/change-password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('merchants.index');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    Route::get('/merchants/print', [MerchantController::class, 'print'])
        ->middleware('permission:print merchants')
        ->name('merchants.print');

    Route::get('/merchants/export', [MerchantController::class, 'export'])
        ->middleware('permission:export merchants')
        ->name('merchants.export');

    Route::patch('/merchants/{merchant}/inline-update', [MerchantController::class, 'inlineUpdate'])
        ->middleware('permission:edit merchants')
        ->name('merchants.inlineUpdate');

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
}

);
