@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="max-w-md mx-auto mt-8 bg-white rounded-2xl shadow-sm border p-8">
    <h1 class="text-2xl font-bold text-center mb-1">Buat Akun Baru</h1>
    <p class="text-center text-gray-500 text-sm mb-6">Mulai catat pendapatan dan atur wallet Anda</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
            <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter, kombinasi huruf dan angka.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <button class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 rounded-lg transition">
            Daftar
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-600 font-medium hover:underline">Masuk di sini</a>
    </p>
</div>
@endsection
