@extends('layouts.app')
@section('title', 'Catat Pendapatan')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-glass-light border border-success border-opacity-30 mb-4">
                <svg class="w-7 h-7 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-text-primary mb-1">Catat Pendapatan Baru</h1>
            <p class="text-text-secondary text-sm">Nominal akan otomatis dibagi sesuai persentase wallet</p>
        </div>

        <!-- Alerts -->
        @if ($walletCount === 0)
            <div class="mb-6 p-4 bg-glass border border-warning border-opacity-30 rounded-lg text-warning text-sm">
                <p class="font-medium mb-1">Belum ada wallet</p>
                <p class="text-xs mb-2">Anda perlu membuat wallet terlebih dahulu untuk mencatat pendapatan.</p>
                <a href="{{ route('wallets.create') }}" class="inline-block text-warning hover:text-warning font-medium underline">Buat wallet sekarang</a>
            </div>
        @elseif (abs((float) $totalPersentase - 100.0) > 0.01)
            <div class="mb-6 p-4 bg-glass border border-warning border-opacity-30 rounded-lg text-warning text-sm">
                <p class="font-medium mb-1">Persentase wallet tidak lengkap</p>
                <p class="text-xs mb-2">Total persentase saat ini: <span class="font-mono font-bold">{{ number_format($totalPersentase, 2) }}%</span> (target: 100%)</p>
                <a href="{{ route('allocations.edit') }}" class="inline-block text-warning hover:text-warning font-medium underline">Sesuaikan persentase</a>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('incomes.store') }}" class="bg-surface-secondary border border-border-light rounded-2xl p-8 shadow-glass space-y-5">
            @csrf

            <!-- Date Field -->
            <div>
                <label for="date" class="block text-sm font-medium text-text-primary mb-2">Tanggal <span class="text-error">*</span></label>
                <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition">
                @error('date')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount Field -->
            <div>
                <label for="amountDisplay" class="block text-sm font-medium text-text-primary mb-2">Nominal <span class="text-error">*</span></label>
                <div class="flex items-center gap-2">
                    <span class="text-text-secondary text-sm font-medium">Rp</span>
                    <input type="text" id="amountDisplay" placeholder="Contoh: 3.500.000" required 
                        class="flex-1 px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition">
                    <input type="hidden" id="amount" name="amount" value="{{ old('amount') }}">
                </div>
                @error('amount')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Source Field -->
            <div>
                <label for="source" class="block text-sm font-medium text-text-primary mb-2">Sumber Pendapatan <span class="text-error">*</span></label>
                <input type="text" id="source" name="source" value="{{ old('source') }}" required 
                    placeholder="Contoh: Gaji, Freelance, Bonus"
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition">
                @error('source')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Note Field -->
            <div>
                <label for="note" class="block text-sm font-medium text-text-primary mb-2">
                    Catatan <span class="text-text-tertiary text-xs font-normal">(Opsional)</span>
                </label>
                <textarea id="note" name="note" rows="3" placeholder="Catatan tambahan tentang pendapatan ini"
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition">{{ old('note') }}</textarea>
                @error('note')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="submit" {{ ($walletCount === 0 || abs((float) $totalPersentase - 100.0) > 0.01) ? 'disabled' : '' }}
                    class="flex-1 bg-success hover:bg-green-700 disabled:bg-text-tertiary disabled:cursor-not-allowed text-white font-semibold py-3 rounded-lg transition">
                    Simpan & Bagikan
                </button>
                <a href="{{ route('incomes.index') }}" class="flex-1 text-center px-4 py-3 bg-glass hover:bg-glass-light border border-border-light hover:border-accent hover:border-opacity-30 text-text-secondary hover:text-accent rounded-lg font-medium transition">
                    Batal
                </a>
            </div>
        </form>

        <!-- Info -->
        <div class="mt-6 p-4 bg-glass border border-accent border-opacity-20 rounded-lg text-accent text-xs">
            <p><span class="font-semibold">Catatan:</span> Nominal akan otomatis dibagi ke semua wallet sesuai persentase yang sudah Anda atur.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const amountDisplay = document.getElementById('amountDisplay');
    const amountHidden = document.getElementById('amount');
    
    // Format number with dots as thousands separator
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    // Parse formatted number to actual number
    function parseFormattedNumber(str) {
        return str.replace(/\./g, '');
    }
    
    // Load previous value if exists
    if (amountHidden.value) {
        amountDisplay.value = formatNumber(amountHidden.value);
    }
    
    // Handle display input
    amountDisplay.addEventListener('input', function() {
        let value = parseFormattedNumber(this.value);
        value = value.replace(/[^0-9]/g, '');
        amountHidden.value = value;
        this.value = value ? formatNumber(value) : '';
    });
    
    // Format on blur
    amountDisplay.addEventListener('blur', function() {
        if (this.value) {
            this.value = formatNumber(parseFormattedNumber(this.value));
        }
    });
</script>
@endpush
@endsection
