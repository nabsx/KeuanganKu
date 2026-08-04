<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManualTransactionRequest;
use App\Http\Requests\StoreWalletDepositRequest;
use App\Jobs\SendTelegramNotification;
use App\Models\Income;
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

    public function depositCreate(Wallet $wallet): View
    {
        $this->authorize('view', $wallet);
        return view('transactions.deposit', compact('wallet'));
    }

    public function deposit(StoreWalletDepositRequest $request, Wallet $wallet): RedirectResponse
    {
        $this->authorize('update', $wallet);
        $validated = $request->validated();
        $newBalance = null;
        $user = Auth::user();
        DB::transaction(function () use ($validated, $wallet, $user, &$newBalance) {
            $date = \Carbon\Carbon::parse($validated['transaction_date']);
            $newBalance = (float) $wallet->balance + (float) $validated['amount'];
            $wallet->update(['balance' => $newBalance]);
            Income::create([
                'user_id' => $user->id,
                'date' => $date,
                'amount' => $validated['amount'],
                'source' => 'Dana manual - '.$wallet->name,
                'note' => $validated['description'],
            ]);
            WalletTransaction::create([
                'wallet_id' => $wallet->id, 'user_id' => $user->id, 'type' => 'in',
                'amount' => $validated['amount'], 'balance_after' => $newBalance,
                'source' => 'manual', 'description' => $validated['description'],
                'transaction_date' => $date, 'month' => $date->month, 'year' => $date->year,
            ]);
            SendTelegramNotification::dispatch($user, "💰 Dana manual berhasil ditambahkan ke wallet <b>{$wallet->name}</b>.\nNominal: Rp".number_format($validated['amount'], 0, ',', '.')."\nCatatan: {$validated['description']}\nSaldo sekarang: Rp".number_format($newBalance, 0, ',', '.'))->afterCommit();
        });
        return redirect()->route('wallets.show', $wallet)->with('success', 'Dana berhasil ditambahkan ke wallet dan pendapatan.');
    }

    public function store(StoreManualTransactionRequest $request, Wallet $wallet): RedirectResponse
    {
        $this->authorize('update', $wallet);

        $validated = $request->validated();
        $user = Auth::user();
        $newBalance = null;

        try {
            DB::transaction(function () use ($validated, $wallet, &$newBalance) {
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
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Terjadi kesalahan saat memproses transaksi.');
        }
    }
}
