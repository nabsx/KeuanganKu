@extends('layouts.app')
@section('title', 'Tambah Wallet')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-glass-light border border-accent border-opacity-30 mb-4">
                <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-text-primary mb-1">Tambah Wallet Baru</h1>
            <p class="text-text-secondary text-sm">Buat wallet untuk mengelola dana Anda</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('wallets.store') }}" class="bg-surface-secondary border border-border-light rounded-2xl p-8 shadow-glass space-y-5">
            @csrf

            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-text-primary mb-2">Nama Wallet <span class="text-error">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus 
                    placeholder="Contoh: Tabungan, Investasi, Uang Makan"
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition">
                @error('name')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description Field -->
            <div>
                <label for="description" class="block text-sm font-medium text-text-primary mb-2">
                    Deskripsi <span class="text-text-tertiary text-xs font-normal">(Opsional)</span>
                </label>
                <input type="text" id="description" name="description" value="{{ old('description') }}" 
                    placeholder="Catatan singkat tentang wallet ini"
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition">
                @error('description')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="submit" class="flex-1 bg-accent hover:bg-accent-dark text-white font-semibold py-3 rounded-lg transition">
                    Simpan Wallet
                </button>
                <a href="{{ route('wallets.index') }}" class="flex-1 text-center px-4 py-3 bg-glass hover:bg-glass-light border border-border-light hover:border-accent hover:border-opacity-30 text-text-secondary hover:text-accent rounded-lg font-medium transition">
                    Batal
                </a>
            </div>
        </form>

        <!-- Info -->
        <div class="mt-6 text-center">
            <p class="text-text-tertiary text-xs">Anda dapat mengatur persentase alokasi wallet setelah membuat wallet</p>
        </div>
    </div>
</div>
@endsection
