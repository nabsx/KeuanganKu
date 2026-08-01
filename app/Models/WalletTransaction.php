<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'income_id',
        'user_id',
        'type',
        'amount',
        'balance_after',
        'source',
        'description',
        'transaction_date',
        'month',
        'year',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function income(): BelongsTo
    {
        return $this->belongsTo(Income::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filter transaksi untuk bulan aktif saat ini.
     */
    public function scopeCurrentMonth(Builder $query): Builder
    {
        $now = now();
        return $query->where('month', $now->month)->where('year', $now->year);
    }

    /**
     * Scope: Filter transaksi untuk bulan/tahun tertentu.
     */
    public function scopeByMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope: Filter transaksi pengeluaran (type = 'out').
     */
    public function scopeExpenses(Builder $query): Builder
    {
        return $query->where('type', 'out');
    }

    /**
     * Scope: Filter transaksi pemasukan (type = 'in').
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', 'in');
    }
}
