@extends('layouts.app')
@section('title', 'Notifikasi Telegram')
@section('content')
<h1 class="text-2xl font-bold mb-6">Pengaturan Notifikasi Telegram</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border p-6">
        <form method="POST" action="{{ route('telegram.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium mb-1">Chat ID Telegram <span class="text-red-500">*</span></label>
                <input type="text" name="chat_id" value="{{ old('chat_id', $setting->chat_id ?? '') }}" required placeholder="Contoh: 123456789"
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
                @error('chat_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2 border-t">
                <label class="block text-sm font-medium mb-2">Bot Token (Opsional)</label>
                <p class="text-xs text-gray-500 mb-2">Kosongkan untuk menggunakan bot token default dari sistem. Atau masukkan bot token Anda sendiri untuk mengirim notifikasi dari bot Anda.</p>
                <textarea name="bot_token" rows="3" placeholder="Masukkan bot token Anda (opsional), atau biarkan kosong"
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border font-mono text-sm">{{ old('bot_token', $setting->bot_token ?? '') }}</textarea>
                @error('bot_token')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm pt-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $setting->is_active ?? false) ? 'checked' : '' }} class="rounded border-gray-300">
                Aktifkan notifikasi Telegram
            </label>
            <div class="flex gap-3 pt-2">
                <button class="bg-primary-600 hover:bg-primary-700 text-white font-medium px-5 py-2.5 rounded-lg transition">Simpan Pengaturan</button>
            </div>
        </form>

        @if (($setting->chat_id ?? null))
            <form method="POST" action="{{ route('telegram.test') }}" class="mt-4 pt-4 border-t">
                @csrf
                <button class="text-sm text-primary-600 hover:underline">Kirim pesan uji coba sekarang</button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 text-sm text-gray-600 space-y-4">
        <div>
            <h2 class="font-semibold text-gray-800 mb-2">Opsi 1: Menggunakan Bot Default (Direkomendasikan)</h2>
            <ol class="list-decimal list-inside space-y-1 text-xs">
                <li>Admin sudah setup bot global di sistem</li>
                <li>Anda hanya perlu set Chat ID Anda</li>
                <li>Notifikasi akan dikirim dari bot global</li>
            </ol>
        </div>

        <div class="border-t pt-4">
            <h2 class="font-semibold text-gray-800 mb-2">Opsi 2: Menggunakan Bot Pribadi Anda</h2>
            <ol class="list-decimal list-inside space-y-2">
                <li>Buka Telegram, cari <strong>@BotFather</strong></li>
                <li>Kirim <code class="bg-gray-100 px-1 rounded">/newbot</code> untuk membuat bot baru</li>
                <li>Salin <strong>token</strong> yang diberikan</li>
                <li>Tempel token di field <strong>Bot Token</strong> di form sebelah kiri</li>
                <li>Mulai chat dengan bot Anda (klik Start)</li>
                <li>Buka di browser: <code class="bg-gray-100 px-1 rounded text-xs break-all">https://api.telegram.org/bot&lt;TOKEN&gt;/getUpdates</code></li>
                <li>Cari nilai <code class="bg-gray-100 px-1 rounded">"chat":{"id": ...}</code>, itulah Chat ID Anda</li>
                <li>Tempel Chat ID di field <strong>Chat ID</strong>, lalu simpan</li>
            </ol>
        </div>

        <div class="border-t pt-4">
            <p class="text-xs text-gray-500"><strong>Catatan:</strong> Jika bot token kosong, sistem akan menggunakan bot default dari konfigurasi admin.</p>
        </div>
    </div>
</div>
@endsection
