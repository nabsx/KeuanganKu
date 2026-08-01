@extends('layouts.app')
@section('title', 'Persentase Wallet')
@section('content')
<h1 class="text-2xl font-bold mb-2">Atur Persentase Pembagian Wallet</h1>
<p class="text-sm text-gray-500 mb-6">Total seluruh persentase wajib tepat <strong>100%</strong>, tidak boleh kurang maupun lebih.</p>

@if ($wallets->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border p-8 text-center">
        <p class="text-gray-400 mb-3">Anda belum memiliki wallet.</p>
        <a href="{{ route('wallets.create') }}" class="text-primary-600 hover:underline">Tambah wallet terlebih dahulu</a>
    </div>
@else
    <form method="POST" action="{{ route('allocations.update') }}" class="bg-white rounded-2xl shadow-sm border p-6" id="allocationForm">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            @foreach ($wallets as $wallet)
                <div class="flex items-center justify-between gap-4">
                    <label class="font-medium text-sm w-1/2 truncate" for="persen_{{ $wallet->id }}">{{ $wallet->name }}</label>
                    <div class="flex items-center gap-2">
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            id="persen_{{ $wallet->id }}"
                            name="persentase[{{ $wallet->id }}]"
                            value="{{ old('persentase.'.$wallet->id, $wallet->allocation->percentage ?? 0) }}"
                            class="allocation-input w-28 rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border text-right"
                        >
                        <span class="text-gray-500 text-sm">%</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between mt-6 pt-4 border-t">
            <div class="text-sm">
                Total: <span id="totalPersen" class="font-bold text-lg">0</span>%
                <span id="totalStatus" class="ml-2 text-xs font-medium"></span>
            </div>
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium px-5 py-2.5 rounded-lg transition">
                Simpan Persentase
            </button>
        </div>
    </form>
@endif

@push('scripts')
<script>
    const inputs = document.querySelectorAll('.allocation-input');
    const totalEl = document.getElementById('totalPersen');
    const statusEl = document.getElementById('totalStatus');

    function hitungTotal() {
        let total = 0;
        inputs.forEach(function (input) {
            total += parseFloat(input.value) || 0;
        });
        total = Math.round(total * 100) / 100;
        totalEl.textContent = total;

        if (total === 100) {
            totalEl.className = 'font-bold text-lg text-green-600';
            statusEl.textContent = '✓ Sudah tepat 100%';
            statusEl.className = 'ml-2 text-xs font-medium text-green-600';
        } else {
            totalEl.className = 'font-bold text-lg text-red-600';
            statusEl.textContent = total > 100 ? '✗ Melebihi 100%' : '✗ Masih kurang dari 100%';
            statusEl.className = 'ml-2 text-xs font-medium text-red-600';
        }
    }

    inputs.forEach(function (input) {
        input.addEventListener('input', hitungTotal);
    });
    hitungTotal();
</script>
@endpush
@endsection
