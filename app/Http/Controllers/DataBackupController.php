<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataBackupController extends Controller
{
    private const VERSION = '1.0';

    public function index(): View
    {
        return view('data-backup.index', ['hasPassword' => filled(Auth::user()->getAuthPassword())]);
    }

    public function export(): StreamedResponse
    {
        $user = Auth::user();
        $wallets = $user->wallets()->with('transactions')->get()->map(fn (Wallet $wallet) => [
            'wallet_name' => $wallet->name,
            'description' => $wallet->description,
            'color' => $wallet->color,
            'percentage' => $wallet->allocation?->percentage,
            'balance' => (float) $wallet->balance,
            'transactions' => $wallet->transactions->map(fn (WalletTransaction $tx) => [
                'amount' => (float) $tx->amount,
                'balance_after' => (float) $tx->balance_after,
                'type' => $tx->type,
                'source' => $tx->source,
                'description' => $tx->description,
                'transaction_date' => optional($tx->transaction_date)->toDateString(),
                'month' => $tx->month,
                'year' => $tx->year,
            ])->values(),
        ])->values();

        $payload = ['app' => 'Keuanganku', 'version' => self::VERSION, 'exported_at' => now()->toIso8601String(), 'user_id' => 'internal', 'wallets' => $wallets];
        return response()->streamDownload(fn () => print(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)), 'keuanganku-backup-'.now()->format('Y-m-d').'.json', ['Content-Type' => 'application/json']);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['backup' => ['required', 'file', 'max:5120', 'mimetypes:application/json,text/plain']]);
        $raw = file_get_contents($request->file('backup')->getRealPath());
        $payload = json_decode($raw, true);
        if (! is_array($payload) || ($payload['app'] ?? null) !== 'Keuanganku' || ($payload['version'] ?? null) !== self::VERSION || ! is_array($payload['wallets'] ?? null)) {
            return back()->with('error', 'Backup tidak kompatibel.');
        }
        foreach ($payload['wallets'] as $wallet) {
            if (! is_array($wallet) || ! is_string($wallet['wallet_name'] ?? null) || ! is_numeric($wallet['balance'] ?? null) || ! is_array($wallet['transactions'] ?? null)) return back()->with('error', 'Format backup tidak valid.');
            foreach ($wallet['transactions'] as $tx) if (! is_array($tx) || ! in_array($tx['type'] ?? null, ['in', 'out'], true) || ! is_numeric($tx['amount'] ?? null)) return back()->with('error', 'Format transaksi backup tidak valid.');
        }
        try {
            DB::transaction(function () use ($payload) {
                $user = Auth::user();
                $user->wallets()->withTrashed()->get()->each(fn (Wallet $wallet) => $wallet->transactions()->withTrashed()->forceDelete());
                $user->wallets()->withTrashed()->forceDelete();
                foreach ($payload['wallets'] as $data) {
                    $wallet = $user->wallets()->create(['name' => $data['wallet_name'], 'description' => $data['description'] ?? null, 'color' => $data['color'] ?? null, 'balance' => $data['balance']]);
                    foreach ($data['transactions'] as $tx) $wallet->transactions()->create(['user_id' => $user->id, 'type' => $tx['type'], 'amount' => $tx['amount'], 'balance_after' => $tx['balance_after'] ?? 0, 'source' => $tx['source'] ?? 'import', 'description' => $tx['description'] ?? null, 'transaction_date' => $tx['transaction_date'] ?? now()->toDateString(), 'month' => $tx['month'] ?? now()->month, 'year' => $tx['year'] ?? now()->year]);
                }
            });
        } catch (\Throwable $e) { return back()->with('error', 'Import gagal dan seluruh perubahan dibatalkan.'); }
        return back()->with('success', 'Backup berhasil diimpor.');
    }

    public function destroyAll(Request $request): RedirectResponse
    {
        $rules = ['confirmation' => ['required', 'in:HAPUS DATA SAYA']];
        if (filled(Auth::user()->getAuthPassword())) $rules['password'] = ['required', 'current_password'];
        $request->validate($rules);
        DB::transaction(function () { $user = Auth::user(); $user->wallets()->get()->each(fn (Wallet $wallet) => $wallet->transactions()->delete()); $user->wallets()->delete(); $user->incomes()->delete(); });
        return back()->with('success', 'Seluruh data finansial berhasil dihapus.');
    }
}
