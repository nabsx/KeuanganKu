<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAllocationRequest;
use App\Models\WalletAllocation;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletAllocationController extends Controller
{
    public function __construct(private TelegramService $telegram)
    {
    }

    public function edit(): View
    {
        $wallets = Auth::user()->wallets()->with('allocation')->orderBy('name')->get();
        $totalPersentase = round((float) $wallets->sum(fn ($w) => (float) ($w->allocation->percentage ?? 0)), 2);

        return view('allocations.edit', compact('wallets', 'totalPersentase'));
    }

    public function update(UpdateAllocationRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated()['persentase'];

        $total = round((float) array_sum($data), 2);

        if ($total !== 100.00) {
            $this->telegram->notifyUser($user, "⚠️ Peringatan: Percobaan mengubah persentase wallet gagal karena totalnya {$total}%, bukan 100%.");

            return back()->withInput()->with('error', "Total persentase harus tepat 100%. Total yang Anda masukkan: {$total}%.");
        }

        DB::transaction(function () use ($data, $user) {
            foreach ($data as $walletId => $percentage) {
                WalletAllocation::updateOrCreate(
                    ['wallet_id' => $walletId, 'user_id' => $user->id],
                    ['percentage' => $percentage]
                );
            }
        });

        $this->telegram->notifyUser($user, '🔧 Persentase pembagian wallet Anda berhasil diperbarui. Total: 100%.');

        return redirect()->route('allocations.edit')->with('success', 'Persentase wallet berhasil diperbarui.');
    }
}
