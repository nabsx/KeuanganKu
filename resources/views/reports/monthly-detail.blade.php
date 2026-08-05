@extends('layouts.app')
@section('title', 'Detail Laporan ' . $selectedDate->format('F Y'))
@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary mb-2">{{ $selectedDate->format('F Y') }}</h1>
            <p class="text-text-secondary">Detail transaksi dan statistik bulanan</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.monthly') }}" class="px-4 py-2 bg-glass hover:bg-glass-light border border-border-light text-text-primary rounded-lg transition font-medium">← Kembali</a>
        </div>
    </div>
</div>

<!-- Monthly Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <!-- Income Card -->
    <div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-glass-light border border-success border-opacity-30 flex items-center justify-center">
                <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-text-tertiary text-sm mb-1">Pendapatan</p>
        <p class="text-2xl md:text-3xl font-bold text-text-primary">Rp {{ number_format($income, 0, ',', '.') }}</p>
    </div>

    <!-- Expenses Card -->
    <div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-glass-light border border-error border-opacity-30 flex items-center justify-center">
                <svg class="w-5 h-5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-text-tertiary text-sm mb-1">Pengeluaran</p>
        <p class="text-2xl md:text-3xl font-bold text-text-primary">Rp {{ number_format($expenses, 0, ',', '.') }}</p>
    </div>

    <!-- Savings Card -->
    <div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-glass-light border border-accent border-opacity-30 flex items-center justify-center">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-text-tertiary text-sm mb-1">Tabungan</p>
        <p class="text-2xl md:text-3xl font-bold text-text-primary">Rp {{ number_format($savings, 0, ',', '.') }}</p>
    </div>
</div>

<!-- Breakdown by Wallet -->
@if ($walletStats->count() > 0)
<div class="bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass mb-8">
    <h2 class="text-lg font-semibold text-text-primary mb-4">Perincian per Wallet</h2>
    <div class="space-y-3">
        @foreach ($walletStats as $stat)
            <div class="p-4 bg-glass border border-border-light rounded-lg hover:border-accent hover:border-opacity-30 transition">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-medium text-text-primary">{{ $stat['name'] }}</h3>
                    <span class="text-xs bg-glass-light border border-accent border-opacity-30 text-accent px-2.5 py-1 rounded-full font-medium">
                        Rp {{ number_format($stat['income'] - $stat['expenses'], 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between text-text-tertiary text-sm">
                    <span class="text-success">Masuk: Rp {{ number_format($stat['income'], 0, ',', '.') }}</span>
                    <span class="text-error">Keluar: Rp {{ number_format($stat['expenses'], 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Incomes Section -->
@if ($incomes->count() > 0)
<div class="bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass mb-8">
    <h2 class="text-lg font-semibold text-text-primary mb-4">Pendapatan</h2>
    <div class="space-y-2">
        @foreach ($incomes as $income_item)
            <div class="p-4 bg-glass border border-border-light rounded-lg hover:border-accent hover:border-opacity-30 transition flex justify-between items-center">
                <div>
                    <p class="font-medium text-text-primary">{{ $income_item->source }}</p>
                    <p class="text-text-tertiary text-sm">{{ $income_item->date->format('d M Y') }}</p>
                    @if ($income_item->note)
                        <p class="text-text-tertiary text-xs mt-1">{{ $income_item->note }}</p>
                    @endif
                </div>
                <p class="text-success font-semibold">+ Rp {{ number_format($income_item->amount, 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Transactions Section -->
<div class="bg-surface-secondary border border-border-light rounded-2xl overflow-hidden shadow-glass">
    <div class="px-6 py-4 border-b border-border-light">
        <h2 class="text-lg font-semibold text-text-primary">Transaksi</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-glass border-b border-border-light">
                <tr>
                    <th class="px-6 py-4 text-left text-text-tertiary text-sm font-semibold">Wallet</th>
                    <th class="px-6 py-4 text-left text-text-tertiary text-sm font-semibold">Deskripsi</th>
                    <th class="px-6 py-4 text-left text-text-tertiary text-sm font-semibold">Tanggal</th>
                    <th class="px-6 py-4 text-right text-text-tertiary text-sm font-semibold">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    <tr class="border-b border-border-light hover:bg-glass transition">
                        <td class="px-6 py-4 text-text-primary font-medium">{{ $trx->wallet?->name ?? 'Wallet dihapus' }}</td>
                        <td class="px-6 py-4 text-text-secondary text-sm">{{ $trx->description ?: 'N/A' }}</td>
                        <td class="px-6 py-4 text-text-tertiary text-sm">{{ $trx->transaction_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right font-medium {{ $trx->type === 'in' ? 'text-success' : 'text-error' }}">
                            {{ $trx->type === 'in' ? '+' : '−' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-text-tertiary">
                            <p>Belum ada transaksi</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($transactions->hasPages())
        <div class="px-6 py-4 border-t border-border-light">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection
