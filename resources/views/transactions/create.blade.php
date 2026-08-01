@extends('layouts.app')
@section('title', 'Transaksi Keluar')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('wallets.show', $wallet) }}" class="inline-flex items-center gap-1 text-accent hover:text-accent-light text-sm font-medium mb-4 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Wallet
            </a>
            <h1 class="text-2xl font-bold text-text-primary mb-1">Transaksi Keluar</h1>
            <p class="text-text-secondary text-sm">{{ $wallet->name }}</p>
        </div>

        <!-- Current Balance Card -->
        <div class="bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass mb-6">
            <p class="text-text-tertiary text-sm mb-2">Saldo Saat Ini</p>
            <p class="text-3xl font-bold text-accent">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
        </div>

        <!-- Form -->
        <form action="{{ route('transactions.store', $wallet) }}" method="POST" class="bg-surface-secondary border border-border-light rounded-2xl p-8 shadow-glass space-y-5">
            @csrf

            <!-- Amount Field -->
            <div>
                <label for="amountDisplay" class="block text-sm font-medium text-text-primary mb-2">
                    Jumlah Uang Keluar <span class="text-error">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-text-secondary text-sm font-medium">Rp</span>
                    <input type="text" id="amountDisplay" placeholder="Contoh: 100.000" required 
                        class="flex-1 px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition @error('amount') border-error @enderror">
                    <input type="hidden" id="amount" name="amount" value="{{ old('amount') }}">
                </div>
                @error('amount')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description Field -->
            <div>
                <label for="description" class="block text-sm font-medium text-text-primary mb-2">
                    Keterangan <span class="text-error">*</span>
                </label>
                <textarea id="description" name="description" rows="3" required 
                    placeholder="Contoh: Bayar cicilan mobil, Biaya sekolah, dll"
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition @error('description') border-error @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Field -->
            <div>
                <label for="transaction_date" class="block text-sm font-medium text-text-primary mb-2">
                    Tanggal Transaksi <span class="text-error">*</span>
                </label>
                <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition @error('transaction_date') border-error @enderror">
                @error('transaction_date')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="submit" class="flex-1 bg-error hover:bg-red-700 text-white font-semibold py-3 rounded-lg transition">
                    Catat Transaksi
                </button>
                <a href="{{ route('wallets.show', $wallet) }}" class="flex-1 text-center px-4 py-3 bg-glass hover:bg-glass-light border border-border-light hover:border-accent hover:border-opacity-30 text-text-secondary hover:text-accent rounded-lg font-medium transition">
                    Batal
                </a>
            </div>
        </form>

        <!-- Warning -->
        <div class="mt-6 p-4 bg-glass border border-error border-opacity-20 rounded-lg text-error text-xs">
            <p><span class="font-semibold">Perhatian:</span> Transaksi keluar akan langsung mengurangi saldo wallet Anda. Pastikan jumlah yang Anda masukkan sudah benar.</p>
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
