@extends('layouts.app')
@section('title', 'Ubah Wallet')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-glass-light border border-accent border-opacity-30 mb-4">
                <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-text-primary mb-1">Ubah Wallet</h1>
            <p class="text-text-secondary text-sm">Edit informasi wallet Anda</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('wallets.update', $wallet) }}" class="bg-surface-secondary border border-border-light rounded-2xl p-8 shadow-glass space-y-5">
            @csrf
            @method('PUT')

            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-text-primary mb-2">Nama Wallet <span class="text-error">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $wallet->name) }}" required autofocus
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
                <input type="text" id="description" name="description" value="{{ old('description', $wallet->description) }}"
                    placeholder="Catatan singkat tentang wallet ini"
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition">
                @error('description')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Balance Info -->
            <div class="p-4 bg-glass border border-border-light rounded-lg">
                <p class="text-text-tertiary text-xs mb-1">Saldo Saat Ini</p>
                <p class="text-lg font-bold text-accent">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="submit" class="flex-1 bg-accent hover:bg-accent-dark text-white font-semibold py-3 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('wallets.index') }}" class="flex-1 text-center px-4 py-3 bg-glass hover:bg-glass-light border border-border-light hover:border-accent hover:border-opacity-30 text-text-secondary hover:text-accent rounded-lg font-medium transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
