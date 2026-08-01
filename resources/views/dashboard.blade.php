@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border p-5">
        <p class="text-sm text-gray-500 mb-1">Total Pendapatan</p>
        <p class="text-2xl font-bold text-primary-700">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border p-5">
        <p class="text-sm text-gray-500 mb-1">Total Saldo Semua Wallet</p>
        <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border p-5">
        <p class="text-sm text-gray-500 mb-1">Total Persentase Wallet</p>
        <p class="text-2xl font-bold {{ $totalPersentase == 100 ? 'text-green-700' : 'text-red-600' }}">{{ number_format($totalPersentase, 2) }}%</p>
        @if ($totalPersentase != 100)
            <a href="{{ route('allocations.edit') }}" class="text-xs text-red-500 mt-1 inline-block hover:underline">Belum 100%, atur sekarang &rarr;</a>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold">Daftar Wallet</h2>
            <a href="{{ route('wallets.index') }}" class="text-sm text-primary-600 hover:underline">Lihat semua</a>
        </div>
        @forelse ($wallets as $wallet)
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium">{{ $wallet->name }}</span>
                    <span class="text-gray-500">Rp {{ number_format($wallet->balance, 0, ',', '.') }} &middot; {{ number_format($wallet->allocation->percentage ?? 0, 0) }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-primary-500 h-2 rounded-full" style="width: {{ min(100, (float) ($wallet->allocation->percentage ?? 0)) }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Belum ada wallet. <a href="{{ route('wallets.create') }}" class="text-primary-600 hover:underline">Tambah wallet</a></p>
        @endforelse
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-5">
        <h2 class="font-semibold mb-4">Distribusi Saldo Wallet</h2>
        @if ($wallets->count() > 0 && $totalSaldo > 0)
            <canvas id="walletChart" height="200"></canvas>
        @else
            <p class="text-sm text-gray-400">Belum ada data saldo untuk ditampilkan.</p>
        @endif
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border p-5 mt-6">
    <h2 class="font-semibold mb-4">Aktivitas Terbaru</h2>
    @forelse ($aktivitasTerbaru as $trx)
        <div class="flex justify-between items-center text-sm py-2 border-b last:border-0">
            <div>
                <p class="font-medium">{{ $trx->wallet->name }}</p>
                <p class="text-gray-400 text-xs">{{ $trx->transaction_date->format('d M Y') }} &middot; {{ $trx->description }}</p>
            </div>
            <span class="font-medium {{ $trx->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                {{ $trx->type === 'in' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
            </span>
        </div>
    @empty
        <p class="text-sm text-gray-400">Belum ada aktivitas.</p>
    @endforelse
</div>

@if ($wallets->count() > 0 && $totalSaldo > 0)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('walletChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: @json($wallets->pluck('name')),
            datasets: [{
                data: @json($wallets->pluck('balance')->map(fn($b) => (float) $b)),
                backgroundColor: ['#2563eb', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16'],
            }],
        },
        options: {
            plugins: { legend: { position: 'bottom' } },
        },
    });
</script>
@endpush
@endif
@endsection
