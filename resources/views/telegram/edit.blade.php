@extends('layouts.app')
@section('title', 'Notifikasi Telegram')
@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-text-primary mb-2">Notifikasi Telegram</h1>
    <p class="text-text-secondary">Terima notifikasi transaksi langsung di Telegram</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Settings Form -->
    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('telegram.update') }}" class="space-y-6 bg-surface-secondary border border-border-light rounded-2xl p-6 shadow-glass">
            @csrf
            @method('PUT')

            <!-- Chat ID Field -->
            <div>
                <label for="chat_id" class="block text-sm font-medium text-text-primary mb-2">
                    Chat ID Telegram <span class="text-error">*</span>
                </label>
                <input type="text" id="chat_id" name="chat_id" value="{{ old('chat_id', $setting->chat_id ?? '') }}" required placeholder="Contoh: 123456789"
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition">
                @error('chat_id')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-text-tertiary text-xs mt-2">ID unik chat Telegram Anda untuk menerima notifikasi</p>
            </div>

            <!-- Bot Token Field -->
            <div class="border-t border-border-light pt-6">
                <label for="bot_token" class="block text-sm font-medium text-text-primary mb-2">
                    Bot Token <span class="text-text-tertiary text-xs font-normal">(Opsional)</span>
                </label>
                <p class="text-text-tertiary text-xs mb-3">Kosongkan untuk menggunakan bot default. Atau gunakan token bot Anda sendiri.</p>
                <textarea id="bot_token" name="bot_token" rows="3" placeholder="Masukkan bot token Anda atau biarkan kosong"
                    class="w-full px-4 py-2.5 bg-glass border border-border-light rounded-lg text-text-primary placeholder-text-tertiary focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent focus:ring-opacity-50 transition font-mono text-sm">{{ old('bot_token', $setting->bot_token ?? '') }}</textarea>
                @error('bot_token')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Enable/Disable Toggle -->
            <div class="border-t border-border-light pt-6">
                <label for="is_active" class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $setting->is_active ?? false) ? 'checked' : '' }} 
                        class="w-5 h-5 rounded bg-glass border border-border-light text-accent focus:ring-accent focus:ring-opacity-50 cursor-pointer">
                    <div>
                        <span class="font-medium text-text-primary">Aktifkan Notifikasi Telegram</span>
                        <p class="text-text-tertiary text-xs mt-0.5">Terima update transaksi ke Telegram</p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6 border-t border-border-light">
                <button type="submit" class="flex-1 bg-accent hover:bg-accent-dark text-white font-semibold py-3 rounded-lg transition">
                    Simpan Pengaturan
                </button>
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

        <!-- Test Button (Outside Form) -->
        @if (($setting->chat_id ?? null))
            <form method="POST" action="{{ route('telegram.test') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full px-4 py-3 bg-glass hover:bg-glass-light border border-accent border-opacity-30 text-accent font-semibold rounded-lg transition">
                    Kirim Pesan Uji Coba
                </button>
            </form>
        @endif
    </div>

    <!-- Help & Instructions -->
    <div class="space-y-4">
        <!-- Default Bot Info -->
        <div class="bg-surface-secondary border border-border-light rounded-2xl p-5 shadow-glass">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-glass-light border border-accent border-opacity-30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-text-primary text-sm">Bot Default (Direkomendasikan)</h3>
                    <ol class="text-text-tertiary text-xs mt-2 space-y-1 list-decimal list-inside">
                        <li>Admin telah setup bot global</li>
                        <li>Cukup masukkan Chat ID Anda</li>
                        <li>Notifikasi siap diterima</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Custom Bot Instructions -->
        <div class="bg-surface-secondary border border-border-light rounded-2xl p-5 shadow-glass">
            <h3 class="font-semibold text-text-primary text-sm mb-3">Menggunakan Bot Pribadi</h3>
            <ol class="text-text-tertiary text-xs space-y-2 list-decimal list-inside">
                <li>Cari <span class="text-accent font-mono">@BotFather</span> di Telegram</li>
                <li>Kirim pesan <span class="text-accent font-mono">/newbot</span></li>
                <li>Salin token yang diberikan</li>
                <li>Tempel di field Bot Token</li>
                <li>Chat dengan bot Anda (klik Start)</li>
                <li>Buka: <code class="text-accent text-xs font-mono break-all">https://api.telegram.org/bot&lt;TOKEN&gt;/getUpdates</code></li>
                <li>Cari <span class="text-accent font-mono">"chat":{"id":</span>, itulah Chat ID</li>
                <li>Simpan pengaturan</li>
            </ol>
        </div>

        <!-- Note -->
        <div class="bg-glass border border-border-light rounded-2xl p-4">
            <p class="text-text-tertiary text-xs"><span class="font-semibold">Catatan:</span> Jika Bot Token kosong, sistem akan menggunakan bot default.</p>
        </div>
    </div>
</div>
@endsection
