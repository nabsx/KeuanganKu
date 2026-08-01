<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\MonthlyTarget;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Get month/year dari request atau gunakan bulan aktif saat ini
        $selectedMonth = request('month', now()->month);
        $selectedYear = request('year', now()->year);
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Wallets dan statistik akumulatif (tidak reset)
        $wallets = $user->wallets()->with('allocation')->orderBy('name')->get();
        $totalSaldo = (float) $wallets->sum('balance');
        $totalPendapatan = (float) $user->incomes()->sum('amount');
        $totalPersentase = round((float) $wallets->sum(fn ($w) => (float) ($w->allocation->percentage ?? 0)), 2);

        // Monthly Statistics
        $monthlyStats = $this->getMonthlyStats($user->id, $selectedMonth, $selectedYear);

        // Target untuk bulan yang dipilih (jika ada)
        $monthlyTarget = MonthlyTarget::where('user_id', $user->id)
            ->byMonth($selectedMonth, $selectedYear)
            ->first();

        $targetProgress = null;
        $targetStatus = null;
        if ($monthlyTarget) {
            $targetProgress = $monthlyTarget->getProgressPercentage($monthlyStats['savings']);
            $targetStatus = $monthlyTarget->getProgressStatus($monthlyStats['savings']);
        }

        // Latest transactions (untuk display di dashboard)
        $aktivitasTerbaru = WalletTransaction::with('wallet')
            ->where('user_id', $user->id)
            ->latest('transaction_date')
            ->latest('id')
            ->take(10)
            ->get();

        // 6-month trend data untuk chart
        $monthlyTrend = $this->getMonthlyTrend($user->id, 6);

        return view('dashboard', [
            'wallets' => $wallets,
            'totalSaldo' => $totalSaldo,
            'totalPendapatan' => $totalPendapatan,
            'totalPersentase' => $totalPersentase,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'monthlyStats' => $monthlyStats,
            'monthlyTarget' => $monthlyTarget,
            'targetProgress' => $targetProgress,
            'targetStatus' => $targetStatus,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'monthlyTrend' => $monthlyTrend,
        ]);
    }

    /**
     * Hitung statistik bulanan: income, expenses, savings.
     */
    private function getMonthlyStats(int $userId, int $month, int $year): array
    {
        $income = (float) Income::where('user_id', $userId)
            ->byMonth($month, $year)
            ->sum('amount');

        $expenses = (float) WalletTransaction::where('user_id', $userId)
            ->byMonth($month, $year)
            ->where('type', 'out')
            ->sum('amount');

        $savings = $income - $expenses;

        return [
            'income' => $income,
            'expenses' => $expenses,
            'savings' => $savings,
        ];
    }

    /**
     * Get 6-month trend untuk chart.
     */
    private function getMonthlyTrend(int $userId, int $months = 6): array
    {
        $trend = [];
        $now = now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $income = (float) Income::where('user_id', $userId)
                ->byMonth($month, $year)
                ->sum('amount');

            $trend[] = [
                'month' => $date->format('M'),
                'year' => $year,
                'income' => $income,
                'label' => $date->format('M Y'),
            ];
        }

        return $trend;
    }
}
