@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="min-h-screen flex flex-col justify-between px-4 py-8">
    <div></div>
    <div class="w-full max-w-md mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/keuanganku-logo.png') }}" alt="KeuanganKu Logo" class="h-16 w-16 object-contain">
            </div>
            <h1 class="text-3xl font-bold mb-2">Buat Akun Baru</h1>
            <p class="text-text-secondary text-sm">Mulai mengelola keuangan Anda sekarang</p>
        </div>

        <!-- Form Container -->
        <div class="bg-surface-secondary border border-border-light rounded-2xl p-8 shadow-glass">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-text-primary mb-2">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition"
                        placeholder="Nama Anda">
                    @error('name')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-text-primary mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
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
                    <p class="text-xs text-text-tertiary mt-1.5">Minimal 8 karakter, gunakan kombinasi huruf dan angka</p>
                    @error('password')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-2">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition"
                        placeholder="••••••••">
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-white font-semibold py-3 rounded-lg transition duration-200 mt-6">
                    Buat Akun
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

            <!-- Login Link -->
            <p class="text-center text-sm text-text-secondary">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-accent font-semibold hover:text-accent-light transition">
                    Masuk di sini
                </a>
            </p>
        </div>

        <!-- Footer Info -->
        <div class="mt-6 text-center text-xs text-text-tertiary">
            <p>Bergabunglah dengan ribuan pengguna yang mengelola keuangan dengan lebih baik</p>
        </div>
    </div>
    
    <!-- Footer Copyright -->
    <div class="text-center text-xs text-text-tertiary py-4">
        &copy; {{ date('Y') }} KeuanganKu — Catat Pendapatan &amp; Manajemen Wallet
    </div>
</div>
@endsection
