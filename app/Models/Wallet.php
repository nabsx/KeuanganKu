<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'color',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Baris konfigurasi persentase pembagian untuk wallet ini.
     */
    public function allocation(): HasOne
    {
        return $this->hasOne(WalletAllocation::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest('transaction_date')->latest('id');
    }

    /**
     * Get monthly statistics untuk wallet tertentu.
     * Return: ['income' => ..., 'expenses' => ..., 'savings' => ...]
     */
    public function getMonthlyStats(int $month, int $year): array
    {
        $income = $this->transactions()
            ->byMonth($month, $year)
            ->where('type', 'in')
            ->sum('amount');

        $expenses = $this->transactions()
            ->byMonth($month, $year)
            ->where('type', 'out')
            ->sum('amount');

        return [
            'income' => (float) $income,
            'expenses' => (float) $expenses,
            'savings' => (float) ($income - $expenses),
        ];
    }
}
