@extends('layouts.app')
@section('title', 'Pendapatan')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Riwayat Pendapatan</h1>
    <a href="{{ route('incomes.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Catat Pendapatan</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="text-left px-4 py-3">Tanggal</th>
                <th class="text-left px-4 py-3">Sumber</th>
                <th class="text-left px-4 py-3">Catatan</th>
                <th class="text-right px-4 py-3">Nominal</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse ($incomes as $income)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $income->date->format('d M Y') }}</td>
                    <td class="px-4 py-3 font-medium">{{ $income->source }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $income->note ?? '-' }}</td>
                    <td class="px-4 py-3 text-right font-medium text-green-600">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada pendapatan tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $incomes->links() }}
</div>
@endsection
