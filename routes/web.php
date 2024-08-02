<?php

use App\Http\Controllers\DistributionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('user-management', UserManagementController::class)->middleware('auth');
Route::resource('distribution', DistributionController::class)->middleware('auth');
Route::get('/top-up-funds', [DistributionController::class, 'topupfunds'])->middleware('auth')->name('top-up-funds.index');
Route::post('/add-fund', [DistributionController::class, 'addfunds'])->middleware('auth')->name('add-funds');
Route::post('/add-beneficiary', [UserManagementController::class, 'addBeneficiary'])->middleware('auth')->name('add-beneficiary');
Route::get('/funds-disbursement', [DistributionController::class, 'fundsdisbursement'])->middleware('auth')->name('funds-disbursement.index');
Route::post('/disburse-funds', [DistributionController::class, 'disburseFunds'])->middleware('auth')->name('disburse-funds');
Route::get('/payout-management', [DistributionController::class, 'payoutmanagement'])->middleware('auth')->name('payout-management');
Route::get('/accounts-management', [DistributionController::class, 'accountsmanagement'])->middleware('auth')->name('accounts-management');
// add-account
Route::post('/add-account', [DistributionController::class, 'addaccount'])->middleware('auth')->name('add-account');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
