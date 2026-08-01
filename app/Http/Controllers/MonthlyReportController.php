<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\MonthlySummary;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MonthlyReportController extends Controller
{
    /**
     * Display laporan bulanan - list 12 bulan terakhir dengan statistik.
     */
    public function index(): View
    {
        $user = Auth::user();
        $now = now();

        // Get 12 bulan terakhir
        $monthlyReports = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $income = (float) Income::where('user_id', $user->id)
                ->byMonth($month, $year)
                ->sum('amount');

            $expenses = (float) WalletTransaction::where('user_id', $user->id)
                ->byMonth($month, $year)
                ->where('type', 'out')
                ->sum('amount');

            $savings = $income - $expenses;

            $monthlyReports[] = [
                'month' => $month,
                'year' => $year,
                'label' => $date->format('F Y'),
                'income' => $income,
                'expenses' => $expenses,
                'savings' => $savings,
            ];
        }

        return view('reports.monthly', compact('monthlyReports'));
    }

    /**
     * Display detail transaksi untuk bulan tertentu.
     */
    public function show(int $month, int $year): View
    {
        $user = Auth::user();

        // Validasi month/year
        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2099) {
            abort(422, 'Invalid month or year');
        }

        $selectedDate = now()
            ->setMonth($month)
            ->setYear($year);

        // Statistik bulan
        $income = (float) Income::where('user_id', $user->id)
            ->byMonth($month, $year)
            ->sum('amount');

        $expenses = (float) WalletTransaction::where('user_id', $user->id)
            ->byMonth($month, $year)
            ->where('type', 'out')
            ->sum('amount');

        $savings = $income - $expenses;

        // Transactions (paginated)
        $transactions = WalletTransaction::with('wallet')
            ->where('user_id', $user->id)
            ->byMonth($month, $year)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        // Incomes untuk bulan ini
        $incomes = Income::where('user_id', $user->id)
            ->byMonth($month, $year)
            ->orderBy('date', 'desc')
            ->get();

        // Breakdown per wallet
        $wallets = $user->wallets()->get();
        $walletStats = $wallets->map(function ($wallet) use ($month, $year) {
            return [
                'name' => $wallet->name,
                'income' => (float) WalletTransaction::where('wallet_id', $wallet->id)
                    ->byMonth($month, $year)
                    ->where('type', 'in')
                    ->sum('amount'),
                'expenses' => (float) WalletTransaction::where('wallet_id', $wallet->id)
                    ->byMonth($month, $year)
                    ->where('type', 'out')
                    ->sum('amount'),
            ];
        })->filter(fn ($stat) => $stat['income'] > 0 || $stat['expenses'] > 0);

        return view('reports.monthly-detail', [
            'selectedDate' => $selectedDate,
            'month' => $month,
            'year' => $year,
            'income' => $income,
            'expenses' => $expenses,
            'savings' => $savings,
            'transactions' => $transactions,
            'incomes' => $incomes,
            'walletStats' => $walletStats,
        ]);
    }
}
