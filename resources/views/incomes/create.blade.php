@extends('layouts.app')
@section('title', 'Catat Pendapatan')
@section('content')
<div class="max-w-lg mx-auto bg-white rounded-2xl shadow-sm border p-8">
    <h1 class="text-xl font-bold mb-2">Catat Pendapatan Baru</h1>
    <p class="text-sm text-gray-500 mb-6">Nominal akan otomatis dibagi ke semua wallet sesuai persentase yang sudah Anda atur.</p>

    @if ($walletCount === 0)
        <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 text-sm">
            Anda belum memiliki wallet. <a href="{{ route('wallets.create') }}" class="underline font-medium">Tambah wallet</a> terlebih dahulu.
        </div>
    @elseif ($totalPersentase != 100)
        <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 text-sm">
            Total persentase wallet Anda saat ini <strong>{{ number_format($totalPersentase, 2) }}%</strong>, belum tepat 100%.
            Silakan <a href="{{ route('allocations.edit') }}" class="underline font-medium">atur persentase</a> terlebih dahulu sebelum mencatat pendapatan.
        </div>
    @endif

    <form method="POST" action="{{ route('incomes.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nominal (Rp)</label>
            <input type="number" step="1" min="1" name="amount" value="{{ old('amount') }}" required placeholder="Contoh: 3500000"
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sumber Pendapatan</label>
            <input type="text" name="source" value="{{ old('source') }}" required placeholder="Contoh: Gaji, Proyek Freelance, Bonus"
                class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
            <textarea name="note" rows="3" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">{{ old('note') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button {{ ($walletCount === 0 || $totalPersentase != 100) ? 'disabled' : '' }}
                class="bg-primary-600 hover:bg-primary-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium px-5 py-2.5 rounded-lg transition">
                Simpan & Bagikan Otomatis
            </button>
            <a href="{{ route('incomes.index') }}" class="px-5 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100">Batal</a>
        </div>
    </form>
</div>
@endsection
