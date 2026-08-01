<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $wallets = $user->wallets()->with('allocation')->orderBy('name')->get();
        $totalSaldo = (float) $wallets->sum('balance');
        $totalPendapatan = (float) $user->incomes()->sum('amount');
        $totalPersentase = round((float) $wallets->sum(fn ($w) => (float) ($w->allocation->percentage ?? 0)), 2);

        $aktivitasTerbaru = WalletTransaction::with('wallet')
            ->where('user_id', $user->id)
            ->latest('transaction_date')
            ->latest('id')
            ->take(10)
            ->get();

        return view('dashboard', compact('wallets', 'totalSaldo', 'totalPendapatan', 'totalPersentase', 'aktivitasTerbaru'));
    }
}
