@extends('layouts.app')
@section('title', 'Riwayat Wallet')
@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-8">
    <div>
        <a href="{{ route('wallets.index') }}" class="inline-flex items-center gap-1 text-accent hover:text-accent-light text-sm font-medium mb-3 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Wallet
        </a>
        <h1 class="text-3xl font-bold text-text-primary">{{ $wallet->name }}</h1>
        @if ($wallet->description)
            <p class="text-text-secondary text-sm mt-1">{{ $wallet->description }}</p>
        @endif
    </div>
    <div class="w-full md:w-auto md:text-right bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass">
        <p class="text-text-tertiary text-sm mb-1">Saldo Saat Ini</p>
        <p class="text-3xl font-bold text-accent mb-4">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
        <a href="{{ route('transactions.create', $wallet) }}" class="inline-flex items-center gap-2 bg-error hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Transaksi Keluar
        </a>
    </div>
</div>

<!-- Table -->
<div class="bg-surface-secondary border border-border-light rounded-2xl overflow-hidden shadow-glass">
    @if ($transactions->count() > 0)
        <table class="w-full text-sm divide-y divide-border-light">
            <thead class="bg-glass border-b border-border-light">
                <tr>
                    <th class="text-left px-6 py-4 font-semibold text-text-secondary text-xs uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-6 py-4 font-semibold text-text-secondary text-xs uppercase tracking-wider">Keterangan</th>
                    <th class="text-left px-6 py-4 font-semibold text-text-secondary text-xs uppercase tracking-wider">Sumber</th>
                    <th class="text-right px-6 py-4 font-semibold text-text-secondary text-xs uppercase tracking-wider">Jumlah</th>
                    <th class="text-right px-6 py-4 font-semibold text-text-secondary text-xs uppercase tracking-wider">Saldo Setelah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
                @foreach ($transactions as $trx)
                    <tr class="hover:bg-glass transition">
                        <td class="px-6 py-4 whitespace-nowrap text-text-primary font-medium">{{ $trx->transaction_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-text-primary">{{ $trx->description }}</td>
                        <td class="px-6 py-4 text-text-tertiary">{{ $trx->source ?? '—' }}</td>
                        <td class="px-6 py-4 text-right font-semibold {{ $trx->type === 'in' ? 'text-success' : 'text-error' }}">
                            {{ $trx->type === 'in' ? '+' : '−' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right text-text-secondary">Rp {{ number_format($trx->balance_after, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border-light bg-glass">
            {{ $transactions->links() }}
        </div>
    @else
        <div class="px-6 py-12 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-12 h-12 rounded-xl bg-glass-light border border-accent border-opacity-30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-text-secondary mb-4">Belum ada riwayat transaksi untuk wallet ini</p>
            <a href="{{ route('transactions.create', $wallet) }}" class="inline-flex items-center gap-2 text-accent hover:text-accent-light font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah transaksi pertama
            </a>
        </div>
    @endif
</div>
@endsection
