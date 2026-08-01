@extends('layouts.app')
@section('title', 'Tambah Transaksi Keluar')
@section('content')
<div class="mb-6">
    <a href="{{ route('wallets.show', $wallet) }}" class="text-sm text-primary-600 hover:underline">&larr; Kembali ke riwayat wallet</a>
    <h1 class="text-2xl font-bold mt-1">Tambah Transaksi Keluar (Cicilan/Pengeluaran)</h1>
    <p class="text-gray-500 text-sm mt-2">Wallet: <span class="font-semibold">{{ $wallet->name }}</span></p>
</div>

<div class="max-w-lg">
    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-6">
        <div class="mb-4 pb-4 border-b">
            <p class="text-sm text-gray-600">Saldo Saat Ini</p>
            <p class="text-3xl font-bold text-green-700">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
        </div>

        <form action="{{ route('transactions.store', $wallet) }}" method="POST" class="space-y-5">
            @csrf

            {{-- Jumlah Uang --}}
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah Uang Keluar <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="amount"
                    name="amount"
                    step="0.01"
                    min="0.01"
                    value="{{ old('amount') }}"
                    placeholder="0"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                />
                @error('amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Contoh: Bayar cicilan mobil, Biaya sekolah, dll"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-red-500 @enderror"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Transaksi --}}
            <div>
                <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Transaksi <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    id="transaction_date"
                    name="transaction_date"
                    value="{{ old('transaction_date', date('Y-m-d')) }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('transaction_date') border-red-500 @enderror"
                />
                @error('transaction_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Submit --}}
            <div class="flex gap-3 pt-4">
                <button
                    type="submit"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition"
                >
                    Catat Transaksi Keluar
                </button>
                <a
                    href="{{ route('wallets.show', $wallet) }}"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg text-center transition"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Info Penting --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-900">
            <strong>ℹ️ Catatan:</strong> Transaksi keluar akan langsung mengurangi saldo wallet Anda. Pastikan jumlah yang Anda masukkan sudah benar.
        </p>
    </div>
</div>
@endsection
