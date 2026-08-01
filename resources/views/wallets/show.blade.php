@extends('layouts.app')
@section('title', 'Riwayat Wallet')
@section('content')
<div class="flex justify-between items-start mb-6">
    <div>
        <a href="{{ route('wallets.index') }}" class="text-sm text-primary-600 hover:underline">&larr; Kembali ke daftar wallet</a>
        <h1 class="text-2xl font-bold mt-1">{{ $wallet->name }}</h1>
        @if ($wallet->description)
            <p class="text-gray-500 text-sm">{{ $wallet->description }}</p>
        @endif
    </div>
    <div class="text-right">
        <p class="text-sm text-gray-500">Saldo Saat Ini</p>
        <p class="text-2xl font-bold text-green-700">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
        <a href="{{ route('transactions.create', $wallet) }}" class="inline-block mt-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
            + Transaksi Keluar
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="text-left px-4 py-3">Tanggal</th>
                <th class="text-left px-4 py-3">Keterangan</th>
                <th class="text-left px-4 py-3">Sumber</th>
                <th class="text-right px-4 py-3">Jumlah</th>
                <th class="text-right px-4 py-3">Saldo Setelah</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse ($transactions as $trx)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $trx->transaction_date->format('d M Y') }}</td>
                    <td class="px-4 py-3">{{ $trx->description }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $trx->source ?? '-' }}</td>
                    <td class="px-4 py-3 text-right font-medium {{ $trx->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $trx->type === 'in' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right text-gray-500">Rp {{ number_format($trx->balance_after, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada riwayat transaksi untuk wallet ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $transactions->links() }}
</div>
@endsection
