@extends('layouts.app')
@section('title', 'Persentase Wallet')
@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-text-primary mb-2">Atur Persentase Pembagian</h1>
    <p class="text-text-secondary">Pastikan total persentase seluruh wallet adalah <span class="font-semibold">100%</span></p>
</div>

@if ($wallets->isEmpty())
    <!-- Empty State -->
    <div class="bg-surface-secondary border border-border-light rounded-2xl p-12 shadow-glass text-center">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 rounded-2xl bg-glass-light border border-accent border-opacity-30 flex items-center justify-center">
                <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <h3 class="text-xl font-semibold text-text-primary mb-2">Belum ada wallet</h3>
        <p class="text-text-secondary mb-6">Buat wallet terlebih dahulu untuk mengatur persentase alokasi</p>
        <a href="{{ route('wallets.create') }}" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white font-semibold px-6 py-3 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Wallet
        </a>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('allocations.update') }}" id="allocationForm" class="bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass">
                @csrf
                @method('PUT')

                <div class="space-y-3 mb-6">
                    @foreach ($wallets as $wallet)
                        <div class="flex items-center gap-4 p-4 bg-glass border border-border-light rounded-lg hover:border-accent hover:border-opacity-30 transition">
                            <div class="flex-1">
                                <label class="font-medium text-text-primary text-sm" for="persen_{{ $wallet->id }}">
                                    {{ $wallet->name }}
                                </label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    id="persen_{{ $wallet->id }}"
                                    name="persentase[{{ $wallet->id }}]"
                                    value="{{ old('persentase.'.$wallet->id, $wallet->allocation->percentage ?? 0) }}"
                                    class="allocation-input w-20 bg-glass border border-border-light rounded-lg text-text-primary text-right px-3 py-2 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition"
                                >
                                <span class="text-text-secondary text-sm font-medium w-6">%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Status Bar -->
                <div class="mb-6 p-4 bg-glass border border-border-light rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-text-tertiary text-sm mb-1">Total Alokasi</p>
                            <p class="text-2xl font-bold" id="totalPersenDisplay">0</p>
                        </div>
                        <div id="statusIndicator" class="text-center">
                            <span id="totalStatus" class="text-sm font-medium text-text-secondary"></span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="w-full bg-accent hover:bg-accent-dark text-white font-semibold py-3 rounded-lg transition">
                    Simpan Persentase
                </button>

                @if ($errors->any())
                    <div class="mt-4 p-4 bg-glass border border-error border-opacity-20 rounded-lg">
                        <p class="text-error text-sm font-medium mb-2">Terjadi kesalahan:</p>
                        <ul class="text-error text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
        </div>

        <!-- Info Sidebar -->
        <div class="space-y-4">
            <!-- Info Card 1 -->
            <div class="bg-surface-secondary border border-border-light rounded-2xl p-5 shadow-glass">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-glass-light border border-accent border-opacity-30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-text-primary text-sm">Persyaratan</h3>
                        <p class="text-text-tertiary text-xs mt-1">Total persentase HARUS tepat 100%. Tidak boleh kurang atau lebih.</p>
                    </div>
                </div>
            </div>

            <!-- Info Card 2 -->
            <div class="bg-surface-secondary border border-border-light rounded-2xl p-5 shadow-glass">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-glass-light border border-info border-opacity-30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-info" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-text-primary text-sm">Contoh</h3>
                        <p class="text-text-tertiary text-xs mt-1">Jika punya 3 wallet: Wallet A: 40%, Wallet B: 35%, Wallet C: 25% = 100% ✓</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-surface-secondary border border-border-light rounded-2xl p-5 shadow-glass">
                <h3 class="font-semibold text-text-primary text-sm mb-3">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button type="button" onclick="distribusiMerata()" class="w-full px-3 py-2 bg-glass hover:bg-glass-light border border-border-light rounded-lg text-text-secondary hover:text-text-primary text-xs font-medium transition">
                        Distribusi Merata
                    </button>
                    <button type="button" onclick="resetPersentase()" class="w-full px-3 py-2 bg-glass hover:bg-glass-light border border-border-light rounded-lg text-text-secondary hover:text-text-primary text-xs font-medium transition">
                        Reset Ke 0
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    const inputs = document.querySelectorAll('.allocation-input');
    const totalPersenDisplay = document.getElementById('totalPersenDisplay');
    const statusEl = document.getElementById('totalStatus');
    const submitBtn = document.getElementById('submitBtn');
    const statusIndicator = document.getElementById('statusIndicator');

    function hitungTotal() {
        let total = 0;
        inputs.forEach(function (input) {
            total += parseFloat(input.value) || 0;
        });
        total = Math.round(total * 100) / 100;
        totalPersenDisplay.textContent = total.toFixed(2);

        if (total === 100) {
            totalPersenDisplay.className = 'text-2xl font-bold text-success';
            statusEl.textContent = '✓ Sempurna 100%';
            statusEl.className = 'text-sm font-medium text-success';
            submitBtn.disabled = false;
            submitBtn.className = 'w-full bg-accent hover:bg-accent-dark text-white font-semibold py-3 rounded-lg transition';
        } else {
            totalPersenDisplay.className = 'text-2xl font-bold text-warning';
            statusEl.textContent = total > 100 ? '✗ Melebihi 100%' : '✗ Kurang dari 100%';
            statusEl.className = 'text-sm font-medium text-warning';
            submitBtn.disabled = true;
            submitBtn.className = 'w-full bg-text-tertiary text-text-secondary font-semibold py-3 rounded-lg cursor-not-allowed opacity-50';
        }
    }

    inputs.forEach(function (input) {
        input.addEventListener('input', hitungTotal);
    });

    function distribusiMerata() {
        const jumlahWallet = inputs.length;
        const persentasePerWallet = (100 / jumlahWallet).toFixed(2);
        inputs.forEach(function (input) {
            input.value = persentasePerWallet;
        });
        hitungTotal();
    }

    function resetPersentase() {
        inputs.forEach(function (input) {
            input.value = 0;
        });
        hitungTotal();
    }

    hitungTotal();
</script>
@endpush
@endsection
