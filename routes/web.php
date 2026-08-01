<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ManualTransactionController;
use App\Http\Controllers\TelegramSettingController;
use App\Http\Controllers\WalletAllocationController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Tamu (belum login)
|--------------------------------------------------------------------------
*/

// Landing page (accessible to everyone)
Route::get('/', function () {
    return view('landing');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Halaman yang Wajib Login
|--------------------------------------------------------------------------
| Setiap query di controller sudah di-scope ke Auth::user() sehingga
| user hanya bisa melihat & mengubah data miliknya sendiri.
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('wallets', WalletController::class)->except(['show']);
    Route::get('/wallets/{wallet}', [WalletController::class, 'show'])->name('wallets.show');
    Route::get('/wallets/{wallet}/transaksi-keluar', [ManualTransactionController::class, 'create'])->name('transactions.create');
    Route::post('/wallets/{wallet}/transaksi-keluar', [ManualTransactionController::class, 'store'])->name('transactions.store');

    Route::get('/pendapatan', [IncomeController::class, 'index'])->name('incomes.index');
    Route::get('/pendapatan/tambah', [IncomeController::class, 'create'])->name('incomes.create');
    Route::post('/pendapatan', [IncomeController::class, 'store'])->name('incomes.store');

    Route::get('/persentase-wallet', [WalletAllocationController::class, 'edit'])->name('allocations.edit');
    Route::put('/persentase-wallet', [WalletAllocationController::class, 'update'])->name('allocations.update');

    Route::get('/pengaturan/telegram', [TelegramSettingController::class, 'edit'])->name('telegram.edit');
    Route::put('/pengaturan/telegram', [TelegramSettingController::class, 'update'])->name('telegram.update');
    Route::post('/pengaturan/telegram/test', [TelegramSettingController::class, 'test'])->name('telegram.test');
});
