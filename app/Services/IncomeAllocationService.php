<?php

namespace App\Services;

use App\Models\Income;
use App\Models\WalletAllocation;
use App\Models\WalletTransaction;
use Illuminate\Support\Collection;

/**
 * Logic inti pembagian nominal pendapatan ke seluruh wallet
 * berdasarkan persentase yang sudah diatur user.
 *
 * Aturan pembulatan: setiap wallet (kecuali yang terakhir) dibulatkan
 * 2 desimal dari (nominal x persentase / 100). Wallet TERAKHIR menerima
 * sisa (nominal - total yang sudah dibagi) sehingga total seluruh alokasi
 * selalu presis sama dengan nominal pendapatan, tidak lebih tidak kurang.
 */
class IncomeAllocationService
{
    public function __construct(private TelegramService $telegram)
    {
    }

    /**
     * @throws \RuntimeException jika total persentase wallet user belum tepat 100%
     */
    public function allocate(Income $income): Collection
    {
        $user = $income->user;

        $allocations = WalletAllocation::with('wallet')
            ->where('user_id', $user->id)
            ->orderBy('id')
            ->get();

        $totalPersentase = round((float) $allocations->sum('percentage'), 2);

        if ($totalPersentase !== 100.00) {
            throw new \RuntimeException("Total persentase wallet saat ini {$totalPersentase}%, harus tepat 100% agar pendapatan bisa dibagi otomatis.");
        }

        $transactions = collect();
        $amount = (float) $income->amount;
        $distributed = 0.0;
        $count = $allocations->count();

        foreach ($allocations->values() as $index => $allocation) {
            $wallet = $allocation->wallet;

            if (! $wallet) {
                continue;
            }

            if ($index === $count - 1) {
                // Wallet terakhir menerima sisa agar total selalu presisi.
                $portion = round($amount - $distributed, 2);
            } else {
                $portion = round($amount * ((float) $allocation->percentage / 100), 2);
                $distributed += $portion;
            }

            $wallet->balance = round((float) $wallet->balance + $portion, 2);
            $wallet->save();

            $trx = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'income_id' => $income->id,
                'user_id' => $user->id,
                'type' => 'in',
                'amount' => $portion,
                'balance_after' => $wallet->balance,
                'source' => $income->source,
                'description' => "Alokasi otomatis {$allocation->percentage}% dari pendapatan \"{$income->source}\"",
                'transaction_date' => $income->date,
            ]);

            $transactions->push($trx);

            $this->telegram->notifyUser(
                $user,
                "💰 Wallet <b>{$wallet->name}</b> menerima alokasi dana sebesar Rp".number_format($portion, 0, ',', '.')
                    ." dari pendapatan \"{$income->source}\".\nSaldo sekarang: Rp".number_format($wallet->balance, 0, ',', '.')
            );
        }

        return $transactions;
    }
}
