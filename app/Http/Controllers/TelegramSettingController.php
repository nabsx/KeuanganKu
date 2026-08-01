<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTelegramSettingRequest;
use App\Models\TelegramSetting;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TelegramSettingController extends Controller
{
    public function __construct(private TelegramService $telegram)
    {
    }

    public function edit(): View
    {
        $setting = Auth::user()->telegramSetting;

        return view('telegram.edit', compact('setting'));
    }

    public function update(UpdateTelegramSettingRequest $request): RedirectResponse
    {
        TelegramSetting::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->validated()
        );

        return redirect()->route('telegram.edit')->with('success', 'Pengaturan notifikasi Telegram berhasil disimpan.');
    }

    public function test(): RedirectResponse
    {
        $user = Auth::user();

        $ok = $this->telegram->notifyUser(
            $user,
            '🔔 Ini adalah pesan uji coba dari aplikasi Catat Pendapatan & Wallet. Notifikasi Telegram Anda sudah aktif!'
        );

        if ($ok) {
            return back()->with('success', 'Pesan uji coba berhasil dikirim ke Telegram Anda. Silakan cek chat dengan bot Anda.');
        }

        return back()->with('error', 'Gagal mengirim pesan uji coba. Pastikan TELEGRAM_BOT_TOKEN di .env sudah benar, Chat ID sudah diisi, dan status Aktif dicentang.');
    }
}
