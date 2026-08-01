<?php

namespace App\Observers;

use App\Models\Income;

class IncomeObserver
{
    /**
     * Handle the Income "creating" event.
     * Auto-extract month/year dari date field.
     */
    public function creating(Income $income): void
    {
        if ($income->date && !$income->month) {
            $income->month = $income->date->month;
            $income->year = $income->date->year;
        }
    }

    /**
     * Handle the Income "updating" event.
     * Auto-extract month/year jika date berubah.
     */
    public function updating(Income $income): void
    {
        if ($income->isDirty('date')) {
            $income->month = $income->date->month;
            $income->year = $income->date->year;
        }
    }
}
