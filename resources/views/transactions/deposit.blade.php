@extends('layouts.app')
@section('title', 'Tambah Dana')
@section('content')
<div class="max-w-2xl mx-auto"><div class="mb-6"><p class="text-accent text-sm">{{ $wallet->name }}</p><h1 class="text-2xl font-bold mt-1">Tambah dana manual</h1><p class="text-text-secondary mt-2">Catat dana masuk ke wallet ini.</p></div>
<form method="POST" action="{{ route('deposits.store', $wallet) }}" class="bg-surface-secondary border border-border-light rounded-xl p-6 space-y-5">@csrf
<div><label class="block text-sm mb-2">Nominal</label><input name="amount" type="number" min="0.01" step="0.01" required value="{{ old('amount') }}" class="w-full rounded-lg bg-surface border border-border-light px-4 py-3 text-text-primary"></div>
<div><label class="block text-sm mb-2">Notes</label><textarea name="description" required maxlength="255" rows="3" class="w-full rounded-lg bg-surface border border-border-light px-4 py-3 text-text-primary">{{ old('description') }}</textarea></div>
<div><label class="block text-sm mb-2">Tanggal</label><input name="transaction_date" type="date" required value="{{ old('transaction_date', now()->toDateString()) }}" class="w-full rounded-lg bg-surface border border-border-light px-4 py-3 text-text-primary"></div>
<div class="flex gap-3"><a href="{{ route('wallets.show', $wallet) }}" class="px-4 py-3 rounded-lg border border-border-light text-text-secondary">Batal</a><button class="px-4 py-3 rounded-lg bg-accent text-background font-semibold">Tambah Dana</button></div></form></div>
@endsection
