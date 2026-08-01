@extends('layouts.app')
@section('title', 'Pendapatan')
@section('content')
<!-- Page Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-text-primary mb-1">Riwayat Pendapatan</h1>
        <p class="text-text-secondary">Semua catatan sumber pendapatan Anda</p>
    </div>
    <a href="{{ route('incomes.create') }}" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white font-semibold px-4 py-3 rounded-lg transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Catat Pendapatan
    </a>
</div>

<!-- Table -->
<div class="bg-surface-secondary border border-border-light rounded-2xl overflow-hidden shadow-glass">
    @if ($incomes->count() > 0)
        <table class="w-full text-sm divide-y divide-border-light">
            <thead class="bg-glass border-b border-border-light">
                <tr>
                    <th class="text-left px-6 py-4 font-semibold text-text-secondary text-xs uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-6 py-4 font-semibold text-text-secondary text-xs uppercase tracking-wider">Sumber</th>
                    <th class="text-left px-6 py-4 font-semibold text-text-secondary text-xs uppercase tracking-wider">Catatan</th>
                    <th class="text-right px-6 py-4 font-semibold text-text-secondary text-xs uppercase tracking-wider">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
                @foreach ($incomes as $income)
                    <tr class="hover:bg-glass transition">
                        <td class="px-6 py-4 whitespace-nowrap text-text-primary font-medium">{{ $income->date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-text-primary font-medium">{{ $income->source }}</td>
                        <td class="px-6 py-4 text-text-tertiary">{{ $income->note ?? '—' }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-success">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border-light bg-glass">
            {{ $incomes->links() }}
        </div>
    @else
        <div class="px-6 py-12 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-12 h-12 rounded-xl bg-glass-light border border-accent border-opacity-30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-text-secondary mb-4">Belum ada pendapatan tercatat</p>
            <a href="{{ route('incomes.create') }}" class="inline-flex items-center gap-2 text-accent hover:text-accent-light font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Catat pendapatan pertama
            </a>
        </div>
    @endif
</div>
@endsection
