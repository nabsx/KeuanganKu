@extends('layouts.app')
@section('title', 'Tambah Dana')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('wallets.show', $wallet) }}" class="inline-flex items-center gap-2 text-text-secondary hover:text-accent text-sm mb-5">← Kembali ke wallet</a>
        <p class="text-accent text-sm">{{ $wallet->name }}</p>
        <h1 class="text-2xl font-bold mt-1 text-text-primary">Tambah dana manual</h1>
        <p class="text-text-secondary mt-2">Dana akan masuk ke saldo wallet dan tercatat sebagai pendapatan.</p>
    </div>
    <form method="POST" action="{{ route('deposits.store', $wallet) }}" class="bg-surface-secondary border border-border-light rounded-xl p-6 space-y-5">
        @csrf
        <div>
            <label for="amountDisplay" class="block text-sm mb-2 text-text-primary">Nominal</label>
            <div class="flex items-center gap-2"><span class="text-text-secondary font-medium">Rp</span><input id="amountDisplay" type="text" inputmode="numeric" autocomplete="off" placeholder="10.000" required value="{{ old('amount') ? number_format((float) old('amount'), 0, ',', '.') : '' }}" class="w-full rounded-lg bg-surface border border-border-light px-4 py-3 text-text-primary"><input id="amount" name="amount" type="hidden" value="{{ old('amount') }}"></div>
            @error('amount')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div><label for="description" class="block text-sm mb-2 text-text-primary">Notes</label><textarea id="description" name="description" required maxlength="255" rows="3" class="w-full rounded-lg bg-surface border border-border-light px-4 py-3 text-text-primary">{{ old('description') }}</textarea>@error('description')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror</div>
        <div><label for="transaction_date" class="block text-sm mb-2 text-text-primary">Tanggal</label><input id="transaction_date" name="transaction_date" type="date" required value="{{ old('transaction_date', now()->toDateString()) }}" class="w-full rounded-lg bg-surface border border-border-light px-4 py-3 text-text-primary"></div>
        <div class="flex gap-3 pt-2"><a href="{{ route('wallets.show', $wallet) }}" class="px-4 py-3 rounded-lg border border-border-light text-text-secondary hover:text-text-primary">Kembali</a><button class="px-4 py-3 rounded-lg bg-accent text-background font-semibold hover:bg-accent-dark">Tambah Dana</button></div>
    </form>
</div>
@push('scripts')
<script>
const display = document.getElementById('amountDisplay'), hidden = document.getElementById('amount');
display.addEventListener('input', () => { const digits = display.value.replace(/\D/g, ''); hidden.value = digits; display.value = digits ? new Intl.NumberFormat('id-ID').format(Number(digits)) : ''; });
</script>
@endpush
@endsection
