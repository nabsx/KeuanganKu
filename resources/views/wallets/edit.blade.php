@extends('layouts.app')
@section('title', 'Ubah Wallet')
@section('content')
<div class="max-w-lg mx-auto bg-white rounded-2xl shadow-sm border p-8">
    <h1 class="text-xl font-bold mb-6">Ubah Wallet</h1>

    <form method="POST" action="{{ route('wallets.update', $wallet) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Nama Wallet</label>
            <input type="text" name="name" value="{{ old('name', $wallet->name) }}" required
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi <span class="text-gray-400 font-normal">(opsional)</span></label>
            <input type="text" name="description" value="{{ old('description', $wallet->description) }}"
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <div class="flex gap-3 pt-2">
            <button class="bg-primary-600 hover:bg-primary-700 text-white font-medium px-5 py-2.5 rounded-lg transition">Simpan Perubahan</button>
            <a href="{{ route('wallets.index') }}" class="px-5 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100">Batal</a>
        </div>
    </form>
</div>
@endsection
