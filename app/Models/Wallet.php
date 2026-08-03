<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

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

    /**
     * IDOR Security: Automatically generate UUID ketika wallet baru dibuat
     * UUID ini digunakan untuk Route Model Binding di URL
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Override Route Key Name untuk menggunakan UUID bukan ID integer
     * Transition-safe: Gunakan uuid jika tersedia (setelah migration), jika tidak fallback ke id
     * Ini akan secara otomatis menggunakan uuid untuk implicit route model binding
     * Contoh: /wallets/{wallet} akan menggunakan UUID, bukan integer ID
     */
    public function getRouteKeyName(): string
    {
        // Check apakah kolom uuid ada di database sebelum menggunakannya
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'uuid')) {
            return 'uuid';
        }
        return 'id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Baris konfigurasi persentase pembagian untuk wallet ini.
     * PENTING: Foreign key tetap menggunakan 'wallet_id' (integer id), bukan uuid
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
