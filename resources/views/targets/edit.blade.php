@extends('layouts.app')
@section('title', 'Target Tabungan - ' . $selectedDate->format('F Y'))
@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-text-primary mb-2">Target Tabungan {{ $selectedDate->format('F Y') }}</h1>
    <p class="text-text-secondary">Tetapkan target tabungan untuk bulan ini</p>
</div>

<!-- Form -->
<div class="max-w-2xl">
    <form method="POST" action="{{ route('targets.update', ['month' => $month, 'year' => $year]) }}" class="bg-surface-secondary border border-border-light rounded-2xl p-8 shadow-glass">
        @csrf
        @method('PUT')

        <!-- Target Amount Field -->
        <div class="mb-8">
            <label for="target_amount" class="block text-sm font-medium text-text-primary mb-2">Target Tabungan <span class="text-error">*</span></label>
            <div class="flex items-center gap-2">
                <span class="text-text-secondary text-sm font-medium">Rp</span>
                <input type="text" id="targetAmountDisplay" placeholder="Contoh: 2.000.000" required 
                    class="flex-1 px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition @error('target_amount') border-error @enderror">
                <input type="hidden" id="target_amount" name="target_amount" value="{{ old('target_amount', $target->target_amount ?? '') }}">
            </div>
            @error('target_amount')
                <p class="text-error text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-accent hover:bg-accent-dark text-white font-semibold py-3 rounded-lg transition">
                Simpan Target
            </button>
            @if ($target->exists)
                <form method="POST" action="{{ route('targets.destroy', ['month' => $month, 'year' => $year]) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin ingin menghapus target ini?')" class="w-full px-4 py-3 bg-glass hover:bg-glass-light border border-error border-opacity-30 text-error font-semibold rounded-lg transition">
                        Hapus Target
                    </button>
                </form>
            @endif
            <a href="{{ route('dashboard', ['month' => $month, 'year' => $year]) }}" class="flex-1 px-4 py-3 bg-glass hover:bg-glass-light border border-border-light text-text-primary font-semibold rounded-lg transition text-center">
                Batal
            </a>
        </div>

        @if ($errors->any())
            <div class="mt-4 p-4 bg-glass border border-error border-opacity-20 rounded-lg">
                <p class="text-error text-sm font-medium mb-2">Terjadi kesalahan:</p>
                <ul class="text-error text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
    const targetAmountDisplay = document.getElementById('targetAmountDisplay');
    const targetAmountHidden = document.getElementById('target_amount');
    
    // Format number with dots as thousands separator
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    // Parse formatted number to actual number
    function parseFormattedNumber(str) {
        return str.replace(/\./g, '');
    }
    
    // Load previous value if exists
    if (targetAmountHidden.value) {
        targetAmountDisplay.value = formatNumber(targetAmountHidden.value);
    }
    
    // Handle display input
    targetAmountDisplay.addEventListener('input', function() {
        let value = parseFormattedNumber(this.value);
        value = value.replace(/[^0-9]/g, '');
        targetAmountHidden.value = value;
        this.value = value ? formatNumber(value) : '';
    });
    
    // Format on blur
    targetAmountDisplay.addEventListener('blur', function() {
        if (this.value) {
            this.value = formatNumber(parseFormattedNumber(this.value));
        }
    });
</script>
@endpush
@endsection
