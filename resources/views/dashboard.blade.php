@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-text-primary mb-2">Dashboard</h1>
    <p class="text-text-secondary">Pantau keuangan dan wallet Anda sekilas</p>
</div>

<!-- Period Selector Card -->
<div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass mb-8">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-text-tertiary text-sm mb-1">Periode</p>
            <h2 class="text-2xl font-bold text-text-primary">
                {{ now()->setMonth($selectedMonth)->setYear($selectedYear)->format('F Y') }}
            </h2>
        </div>
        <div class="flex gap-2">
            @php
                $prevMonth = $selectedMonth - 1;
                $prevYear = $selectedYear;
                if ($prevMonth < 1) {
                    $prevMonth = 12;
                    $prevYear--;
                }
                
                $nextMonth = $selectedMonth + 1;
                $nextYear = $selectedYear;
                if ($nextMonth > 12) {
                    $nextMonth = 1;
                    $nextYear++;
                }
            @endphp
            <a href="{{ route('dashboard', ['month' => $prevMonth, 'year' => $prevYear]) }}" 
               class="px-4 py-2 bg-glass hover:bg-glass-light border border-border-light text-text-primary rounded-lg transition font-medium">
                ← Sebelumnya
            </a>
            @if ($selectedMonth !== $currentMonth || $selectedYear !== $currentYear)
                <a href="{{ route('dashboard') }}" 
                   class="px-4 py-2 bg-glass hover:bg-glass-light border border-border-light text-text-primary rounded-lg transition font-medium">
                    Bulan Ini
                </a>
            @endif
            <a href="{{ route('dashboard', ['month' => $nextMonth, 'year' => $nextYear]) }}" 
               class="px-4 py-2 bg-glass hover:bg-glass-light border border-border-light text-text-primary rounded-lg transition font-medium">
                Selanjutnya →
            </a>
        </div>
    </div>
</div>

<!-- Monthly Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <!-- Monthly Income Card -->
    <div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-glass-light border border-success border-opacity-30 flex items-center justify-center">
                <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-text-tertiary text-sm mb-1">Pendapatan Bulan Ini</p>
        <p class="text-2xl md:text-3xl font-bold text-text-primary">Rp {{ number_format($monthlyStats['income'], 0, ',', '.') }}</p>
    </div>

    <!-- Monthly Expenses Card -->
    <div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-glass-light border border-error border-opacity-30 flex items-center justify-center">
                <svg class="w-5 h-5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-text-tertiary text-sm mb-1">Pengeluaran Bulan Ini</p>
        <p class="text-2xl md:text-3xl font-bold text-text-primary">Rp {{ number_format($monthlyStats['expenses'], 0, ',', '.') }}</p>
    </div>

    <!-- Monthly Savings Card -->
    <div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-glass-light border border-accent border-opacity-30 flex items-center justify-center">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-text-tertiary text-sm mb-1">Tabungan Bulan Ini</p>
        <p class="text-2xl md:text-3xl font-bold text-text-primary">Rp {{ number_format($monthlyStats['savings'], 0, ',', '.') }}</p>
    </div>
</div>

<!-- Target Card (if exists) -->
@if ($monthlyTarget)
<div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-text-primary">Target Tabungan</h3>
            <p class="text-text-tertiary text-sm mt-1">Rp {{ number_format($monthlyTarget->target_amount, 0, ',', '.') }}</p>
        </div>
        <a href="{{ route('targets.edit', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" 
           class="text-accent hover:text-accent-light text-sm font-medium transition">Edit</a>
    </div>
    <div class="mb-3">
        <div class="flex justify-between mb-2">
            <p class="text-text-tertiary text-sm">Progress Pencapaian</p>
            <p class="text-text-primary font-semibold">{{ number_format($targetProgress, 1) }}%</p>
        </div>
        <div class="w-full bg-glass rounded-full h-3 overflow-hidden">
            <div class="h-3 rounded-full transition-all duration-500 {{ $targetStatus === 'success' ? 'bg-success' : ($targetStatus === 'warning' ? 'bg-warning' : 'bg-error') }}" 
                 style="width: {{ min(100, $targetProgress) }}%"></div>
        </div>
    </div>
    <p class="text-text-tertiary text-xs">
        Rp {{ number_format($monthlyStats['savings'], 0, ',', '.') }} dari Rp {{ number_format($monthlyTarget->target_amount, 0, ',', '.') }}
    </p>
</div>
@else
<div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass mb-8">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-text-tertiary text-sm mb-1">Target Tabungan</p>
            <p class="text-text-primary font-medium">Belum ada target</p>
        </div>
        <a href="{{ route('targets.edit', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" 
           class="px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-lg transition font-medium text-sm">
            Buat Target
        </a>
    </div>
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <!-- Total Income Card -->
    <div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-glass-light border border-accent border-opacity-30 flex items-center justify-center">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-text-tertiary text-sm mb-1">Total Pendapatan</p>
        <p class="text-2xl md:text-3xl font-bold text-text-primary">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
    </div>

    <!-- Total Balance Card -->
    <div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-glass-light border border-success border-opacity-30 flex items-center justify-center">
                <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-text-tertiary text-sm mb-1">Total Saldo Wallet</p>
        <p class="text-2xl md:text-3xl font-bold text-text-primary">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</p>
    </div>

    <!-- Allocation Progress Card -->
    <div class="bg-gradient-to-br from-glass-light to-glass border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg {{ abs((float) $totalPersentase - 100.0) <= 0.01 ? 'bg-glass-light border border-success border-opacity-30' : 'bg-glass-light border border-warning border-opacity-30' }} flex items-center justify-center">
                <svg class="w-5 h-5 {{ $totalPersentase == 100 ? 'text-success' : 'text-warning' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-text-tertiary text-sm mb-1">Persentase Alokasi</p>
        <p class="text-2xl md:text-3xl font-bold {{ $totalPersentase == 100 ? 'text-success' : 'text-warning' }}">{{ number_format($totalPersentase, 2) }}%</p>
        @if ($totalPersentase != 100)
            <a href="{{ route('allocations.edit') }}" class="inline-block mt-2 text-xs text-warning hover:text-warning font-medium transition">Sesuaikan alokasi &rarr;</a>
        @endif
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Wallets Overview -->
    <div class="lg:col-span-2 bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-semibold text-text-primary">Daftar Wallet</h2>
                <p class="text-text-tertiary text-sm mt-1">Lihat saldo dan persentase alokasi</p>
            </div>
            <a href="{{ route('wallets.index') }}" class="text-accent hover:text-accent-light text-sm font-medium transition">Lihat semua →</a>
        </div>

        @forelse ($wallets as $wallet)
            <div class="mb-4 p-4 rounded-lg bg-glass border border-border-light hover:border-accent hover:border-opacity-30 transition">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-medium text-text-primary">{{ $wallet->name }}</h3>
                        <p class="text-text-tertiary text-sm">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                    </div>
                    <span class="text-xs bg-glass-light border border-accent border-opacity-30 text-accent px-2.5 py-1 rounded-full font-medium">{{ number_format($wallet->allocation->percentage ?? 0, 0) }}%</span>
                </div>
                <div class="w-full bg-glass rounded-full h-2 overflow-hidden">
                    <div class="bg-accent h-2 rounded-full transition-all duration-500" style="width: {{ min(100, (float) ($wallet->allocation->percentage ?? 0)) }}%"></div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <p class="text-text-tertiary mb-3">Belum ada wallet</p>
                <a href="{{ route('wallets.create') }}" class="inline-block text-accent hover:text-accent-light font-medium transition">Tambah wallet pertama →</a>
            </div>
        @endforelse
    </div>

    <!-- Distribution Chart -->
    <div class="bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass">
        <h2 class="text-lg font-semibold text-text-primary mb-1">Distribusi Saldo</h2>
        <p class="text-text-tertiary text-sm mb-4">Perbandingan antar wallet</p>
        @if ($wallets->count() > 0 && $totalSaldo > 0)
            <canvas id="walletChart" height="180"></canvas>
        @else
            <div class="flex items-center justify-center h-40">
                <p class="text-text-tertiary text-sm">Belum ada data</p>
            </div>
        @endif
    </div>
</div>

<!-- Quick Links -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    <a href="{{ route('reports.monthly') }}" class="bg-gradient-to-br from-glass-light to-glass border border-border-light hover:border-accent hover:border-opacity-50 rounded-2xl p-6 shadow-glass transition">
        <div class="w-10 h-10 rounded-lg bg-glass-light border border-accent border-opacity-30 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-text-primary mb-1">Laporan Bulanan</h3>
        <p class="text-text-tertiary text-sm">Lihat detail transaksi dan statistik per bulan</p>
    </a>
</div>

<!-- Recent Activity -->
<div class="bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-text-primary">Aktivitas Terbaru</h2>
        <p class="text-text-tertiary text-sm mt-1">Transaksi terakhir di semua wallet</p>
    </div>

    @forelse ($aktivitasTerbaru as $trx)
        <div class="flex justify-between items-center py-3 px-4 rounded-lg hover:bg-glass transition {{ !$loop->last ? 'border-b border-border-light' : '' }}">
            <div class="flex-1">
                <p class="font-medium text-text-primary">{{ $trx->wallet?->name ?? 'Wallet dihapus' }}</p>
                <p class="text-text-tertiary text-xs mt-0.5">{{ $trx->transaction_date?->format('d M Y') ?? 'Tanggal tidak tersedia' }} • {{ $trx->description }}</p>
            </div>
            <span class="font-semibold {{ $trx->type === 'in' ? 'text-success' : 'text-error' }} text-right">
                {{ $trx->type === 'in' ? '+' : '−' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
            </span>
        </div>
    @empty
        <div class="text-center py-8">
            <p class="text-text-tertiary">Belum ada aktivitas</p>
        </div>
    @endforelse
</div>

@if ($wallets->count() > 0 && $totalSaldo > 0)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('walletChart');
    const colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16'];
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: @json($wallets->pluck('name')),
            datasets: [{
                data: @json($wallets->pluck('balance')->map(fn($b) => (float) $b)),
                backgroundColor: colors,
                borderColor: '#0a0a0a',
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        color: '#a0a0a0',
                        font: { size: 12 },
                        padding: 12,
                    }
                },
            },
        },
    });
</script>
@endpush
@endif
@endsection
