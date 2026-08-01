@extends('layouts.app')
@section('title', 'Wallet')
@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-text-primary mb-1">Wallet Saya</h1>
        <p class="text-text-secondary">Kelola semua wallet dan alokasi dana Anda</p>
    </div>
    <a href="{{ route('wallets.create') }}" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white font-semibold px-4 py-3 rounded-lg transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Wallet
    </a>
</div>

@if ($wallets->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($wallets as $wallet)
            <div class="group bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass hover:border-accent hover:border-opacity-40 transition duration-300">
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-semibold text-lg text-text-primary">{{ $wallet->name }}</h3>
                        @if ($wallet->description)
                            <p class="text-text-tertiary text-sm mt-1">{{ $wallet->description }}</p>
                        @endif
                    </div>
                    <span class="text-xs bg-glass-light border border-accent border-opacity-30 text-accent px-3 py-1.5 rounded-full font-medium whitespace-nowrap">
                        {{ number_format($wallet->allocation->percentage ?? 0, 0) }}%
                    </span>
                </div>

                <!-- Balance -->
                <p class="text-3xl font-bold text-text-primary mb-1">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                <p class="text-text-tertiary text-xs mb-6">Saldo Saat Ini</p>

                <!-- Allocation Progress -->
                <div class="mb-6">
                    <div class="w-full bg-glass rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-accent to-accent-light h-1.5 rounded-full transition-all duration-500" 
                             style="width: {{ min(100, (float) ($wallet->allocation->percentage ?? 0)) }}%"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('wallets.show', $wallet) }}" class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-glass hover:bg-glass-light border border-border-light hover:border-accent hover:border-opacity-30 rounded-lg text-text-secondary hover:text-accent text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Riwayat
                    </a>
                    <a href="{{ route('wallets.edit', $wallet) }}" class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-glass hover:bg-glass-light border border-border-light hover:border-accent hover:border-opacity-30 rounded-lg text-text-secondary hover:text-accent text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Ubah
                    </a>
                    <form method="POST" action="{{ route('wallets.destroy', $wallet) }}" class="flex-1" onsubmit="return confirm('Hapus wallet {{ $wallet->name }}? Seluruh riwayat transaksi akan ikut terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-glass hover:bg-glass-light border border-border-light hover:border-error hover:border-opacity-30 rounded-lg text-text-secondary hover:text-error text-sm font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Empty State -->
    <div class="bg-surface-secondary border border-border-light rounded-2xl p-12 shadow-glass text-center">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 rounded-2xl bg-glass-light border border-accent border-opacity-30 flex items-center justify-center">
                <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <h3 class="text-xl font-semibold text-text-primary mb-2">Belum ada wallet</h3>
        <p class="text-text-secondary mb-6">Mulai dengan membuat wallet pertama untuk mengelola dana Anda</p>
        <a href="{{ route('wallets.create') }}" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white font-semibold px-6 py-3 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Wallet Pertama
        </a>
    </div>
@endif
@endsection
