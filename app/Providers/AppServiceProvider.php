<?php

namespace App\Providers;

use App\Models\Income;
use App\Models\WalletTransaction;
use App\Observers\IncomeObserver;
use App\Observers\WalletTransactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers untuk auto-extract month/year
        Income::observe(IncomeObserver::class);
        WalletTransaction::observe(WalletTransactionObserver::class);
    }
}
