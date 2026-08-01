<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'amount',
        'source',
        'note',
        'month',
        'year',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Riwayat pembagian dana ke masing-masing wallet dari pendapatan ini.
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Scope: Filter income untuk bulan aktif saat ini.
     */
    public function scopeCurrentMonth(Builder $query): Builder
    {
        $now = now();
        return $query->where('month', $now->month)->where('year', $now->year);
    }

    /**
     * Scope: Filter income untuk bulan/tahun tertentu.
     */
    public function scopeByMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->where('month', $month)->where('year', $year);
    }
}
