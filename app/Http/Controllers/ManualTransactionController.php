<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManualTransactionRequest;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ManualTransactionController extends Controller
{
    public function __construct(private TelegramService $telegram)
    {
    }

    public function create(Wallet $wallet): View
    {
        $this->authorize('view', $wallet);

        return view('transactions.create', compact('wallet'));
    }

    public function store(StoreManualTransactionRequest $request, Wallet $wallet): RedirectResponse
    {
        $this->authorize('update', $wallet);

        $validated = $request->validated();
        $user = Auth::user();
        $newBalance = null;

        DB::transaction(function () use ($validated, $wallet, &$newBalance) {
            // Validasi: jangan biarkan balance menjadi negatif
            if ($wallet->balance < $validated['amount']) {
                throw new \Exception('Saldo wallet tidak cukup.');
            }

            // Update saldo wallet (kurangi)
            $newBalance = $wallet->balance - $validated['amount'];
            $wallet->update(['balance' => $newBalance]);

            // Buat transaksi manual (type='out')
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'amount' => $validated['amount'],
                'balance_after' => $newBalance,
                'source' => 'manual',
                'description' => $validated['description'],
                'transaction_date' => $validated['transaction_date'],
            ]);
        });

        // Kirim notifikasi Telegram
        $this->telegram->notifyUser(
            $user,
            "❌ Pengeluaran dari wallet <b>{$wallet->name}</b> berhasil dicatat!\n"
            . "Keterangan: {$validated['description']}\n"
            . "Nominal: Rp " . number_format($validated['amount'], 0, ',', '.') . "\n"
            . "Saldo sekarang: Rp " . number_format($newBalance, 0, ',', '.')
        );

        return redirect()
            ->route('wallets.show', $wallet)
            ->with('success', "Transaksi keluar sebesar Rp " . number_format($validated['amount'], 0, ',', '.') . " berhasil dicatat.");
    }
}
