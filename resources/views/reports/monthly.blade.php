@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary mb-2">Laporan Bulanan</h1>
            <p class="text-text-secondary">Histori transaksi dan statistik 12 bulan terakhir</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-accent hover:text-accent-light font-medium transition">← Kembali ke Dashboard</a>
    </div>
</div>

<!-- Reports Table -->
<div class="bg-surface-secondary border border-border-light rounded-2xl overflow-hidden shadow-glass">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-glass border-b border-border-light">
                <tr>
                    <th class="px-6 py-4 text-left text-text-tertiary text-sm font-semibold">Bulan</th>
                    <th class="px-6 py-4 text-right text-text-tertiary text-sm font-semibold">Pendapatan</th>
                    <th class="px-6 py-4 text-right text-text-tertiary text-sm font-semibold">Pengeluaran</th>
                    <th class="px-6 py-4 text-right text-text-tertiary text-sm font-semibold">Tabungan</th>
                    <th class="px-6 py-4 text-center text-text-tertiary text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($monthlyReports as $report)
                    <tr class="border-b border-border-light hover:bg-glass transition">
                        <td class="px-6 py-4 text-text-primary font-medium">
                            <a href="{{ route('reports.monthly-detail', ['month' => $report['month'], 'year' => $report['year']]) }}" 
                               class="text-accent hover:text-accent-light transition">
                                {{ $report['label'] }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-right text-success font-medium">
                            Rp {{ number_format($report['income'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right text-error font-medium">
                            Rp {{ number_format($report['expenses'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right text-accent font-medium">
                            Rp {{ number_format($report['savings'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('reports.monthly-detail', ['month' => $report['month'], 'year' => $report['year']]) }}" 
                               class="text-accent hover:text-accent-light font-medium text-sm transition">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-text-tertiary">
                            <p>Belum ada laporan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
