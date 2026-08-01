<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'target_amount',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get target untuk bulan aktif saat ini.
     */
    public function scopeCurrentMonth(Builder $query): Builder
    {
        $now = now();
        return $query->where('month', $now->month)->where('year', $now->year);
    }

    /**
     * Scope: Get target untuk bulan/tahun tertentu.
     */
    public function scopeByMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Calculate progress percentage.
     * $achieved: total tabungan bulan ini
     */
    public function getProgressPercentage(float $achieved): float
    {
        if ($this->target_amount == 0) {
            return 0;
        }
        return min(100, ($achieved / (float) $this->target_amount) * 100);
    }

    /**
     * Get status berdasarkan progress percentage.
     * Green: >=80%, Yellow: 50-79%, Red: <50%
     */
    public function getProgressStatus(float $achieved): string
    {
        $percentage = $this->getProgressPercentage($achieved);

        if ($percentage >= 80) {
            return 'success'; // Green
        } elseif ($percentage >= 50) {
            return 'warning'; // Yellow
        }
        return 'danger'; // Red
    }
}
