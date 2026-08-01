@extends('layouts.app')
@section('title', 'Wallet')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Wallet Saya</h1>
    <a href="{{ route('wallets.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Tambah Wallet</a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse ($wallets as $wallet)
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-lg">{{ $wallet->name }}</h3>
                <span class="text-xs bg-primary-50 text-primary-700 px-2 py-1 rounded-full whitespace-nowrap">{{ number_format($wallet->allocation->percentage ?? 0, 0) }}%</span>
            </div>
            <p class="text-2xl font-bold text-gray-800 mb-1">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
            @if ($wallet->description)
                <p class="text-sm text-gray-500 mb-3">{{ $wallet->description }}</p>
            @endif
            <div class="flex gap-3 mt-4 text-sm">
                <a href="{{ route('wallets.show', $wallet) }}" class="text-primary-600 hover:underline">Riwayat</a>
                <a href="{{ route('wallets.edit', $wallet) }}" class="text-gray-500 hover:underline">Ubah</a>
                <form method="POST" action="{{ route('wallets.destroy', $wallet) }}" onsubmit="return confirm('Hapus wallet {{ $wallet->name }}? Seluruh riwayat transaksi wallet ini juga akan ikut terhapus.')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-500 hover:underline">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm border p-8 text-center">
            <p class="text-gray-400 mb-3">Belum ada wallet.</p>
            <a href="{{ route('wallets.create') }}" class="text-primary-600 hover:underline">Tambah wallet pertama Anda</a>
        </div>
    @endforelse
</div>
@endsection
