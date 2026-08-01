@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-glass-light border border-accent border-opacity-30 mb-4">
                <svg class="w-7 h-7 text-accent" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.5 3a1.5 1.5 0 00-1.5 1.5v5a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-5a1.5 1.5 0 00-1.5-1.5h-3zm8 0a1.5 1.5 0 00-1.5 1.5v3a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-3a1.5 1.5 0 00-1.5-1.5h-3zm-8 9a1.5 1.5 0 00-1.5 1.5v3a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-3a1.5 1.5 0 00-1.5-1.5h-3zm8 0a1.5 1.5 0 00-1.5 1.5v3a1.5 1.5 0 001.5 1.5h3a1.5 1.5 0 001.5-1.5v-3a1.5 1.5 0 00-1.5-1.5h-3z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold mb-2">Masuk ke KeuanganKu</h1>
            <p class="text-text-secondary text-sm">Kelola pendapatan dan wallet Anda dengan mudah</p>
        </div>

        <!-- Form Container -->
        <div class="bg-surface-secondary border border-border-light rounded-2xl p-8 shadow-glass">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-text-primary mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition"
                        placeholder="nama@contoh.com">
                    @error('email')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-text-primary mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition"
                        placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <label for="remember" class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded bg-glass border border-border-light text-accent focus:ring-accent focus:ring-opacity-50 cursor-pointer">
                    <span class="text-sm text-text-secondary">Ingat saya di perangkat ini</span>
                </label>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-white font-semibold py-3 rounded-lg transition duration-200 mt-6">
                    Masuk Sekarang
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-border-light"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-surface-secondary text-text-tertiary">atau</span>
                </div>
            </div>

            <!-- Register Link -->
            <p class="text-center text-sm text-text-secondary">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-accent font-semibold hover:text-accent-light transition">
                    Daftar sekarang
                </a>
            </p>
        </div>

        <!-- Footer Info -->
        <div class="mt-6 text-center text-xs text-text-tertiary">
            <p>Aman dan terpercaya untuk mengelola keuangan Anda</p>
        </div>
    </div>
</div>
@endsection
