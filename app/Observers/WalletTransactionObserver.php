<?php

namespace App\Observers;

use App\Models\WalletTransaction;

class WalletTransactionObserver
{
    /**
     * Handle the WalletTransaction "creating" event.
     * Auto-extract month/year dari transaction_date field.
     */
    public function creating(WalletTransaction $transaction): void
    {
        if ($transaction->transaction_date && !$transaction->month) {
            $transaction->month = $transaction->transaction_date->month;
            $transaction->year = $transaction->transaction_date->year;
        }
    }

    /**
     * Handle the WalletTransaction "updating" event.
     * Auto-extract month/year jika transaction_date berubah.
     */
    public function updating(WalletTransaction $transaction): void
    {
        if ($transaction->isDirty('transaction_date')) {
            $transaction->month = $transaction->transaction_date->month;
            $transaction->year = $transaction->transaction_date->year;
        }
    }
}
