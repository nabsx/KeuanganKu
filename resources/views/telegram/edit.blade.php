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
                <label class="block text-sm font-medium mb-1">Chat ID Telegram</label>
                <input type="text" name="chat_id" value="{{ old('chat_id', $setting->chat_id ?? '') }}" required placeholder="Contoh: 123456789"
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 px-3 py-2 border">
            </div>
            <label class="flex items-center gap-2 text-sm">
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

    <div class="bg-white rounded-2xl shadow-sm border p-6 text-sm text-gray-600 space-y-3">
        <h2 class="font-semibold text-gray-800">Cara Mendapatkan Chat ID</h2>
        <ol class="list-decimal list-inside space-y-2">
            <li>Buka Telegram, cari <strong>@BotFather</strong>, kirim <code class="bg-gray-100 px-1 rounded">/newbot</code> untuk membuat bot baru dan salin <strong>token</strong> yang diberikan.</li>
            <li>Isi token tersebut ke variabel <code class="bg-gray-100 px-1 rounded">TELEGRAM_BOT_TOKEN</code> di file <code class="bg-gray-100 px-1 rounded">.env</code> aplikasi (lihat README).</li>
            <li>Mulai chat dengan bot Anda (klik Start / kirim pesan apa saja ke bot tersebut).</li>
            <li>Buka di browser: <code class="bg-gray-100 px-1 rounded break-all">https://api.telegram.org/bot&lt;TOKEN&gt;/getUpdates</code></li>
            <li>Cari nilai <code class="bg-gray-100 px-1 rounded">"chat":{"id": ...}</code> pada hasilnya, itulah Chat ID Anda.</li>
            <li>Masukkan Chat ID tersebut di form sebelah kiri, centang Aktifkan, lalu simpan.</li>
        </ol>
    </div>
</div>
@endsection
