<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_income',
        'total_expense',
        'total_savings',
    ];

    protected $casts = [
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'total_savings' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get summary untuk bulan aktif saat ini.
     */
    public function scopeCurrentMonth(Builder $query): Builder
    {
        $now = now();
        return $query->where('month', $now->month)->where('year', $now->year);
    }

    /**
     * Scope: Get summary untuk bulan/tahun tertentu.
     */
    public function scopeByMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Create or update summary untuk bulan tertentu.
     * Hitung dari incomes dan wallet_transactions.
     */
    public static function updateSummary(int $userId, int $month, int $year): self
    {
        $totalIncome = Income::where('user_id', $userId)
            ->byMonth($month, $year)
            ->sum('amount');

        $totalExpense = WalletTransaction::where('user_id', $userId)
            ->byMonth($month, $year)
            ->where('type', 'out')
            ->sum('amount');

        $totalSavings = $totalIncome - $totalExpense;

        return static::updateOrCreate(
            ['user_id' => $userId, 'month' => $month, 'year' => $year],
            [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'total_savings' => $totalSavings,
            ]
        );
    }
}
