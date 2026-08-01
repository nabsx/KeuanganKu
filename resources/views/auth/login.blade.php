@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="max-w-md mx-auto mt-8 bg-white rounded-2xl shadow-sm border p-8">
    <h1 class="text-2xl font-bold text-center mb-1">Masuk ke Akun</h1>
    <p class="text-center text-gray-500 text-sm mb-6">Kelola pendapatan dan wallet Anda</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" required
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300">
            Ingat saya
        </label>
        <button class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 rounded-lg transition">
            Masuk
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Belum punya akun? <a href="{{ route('register') }}" class="text-primary-600 font-medium hover:underline">Daftar sekarang</a>
    </p>
</div>
@endsection
