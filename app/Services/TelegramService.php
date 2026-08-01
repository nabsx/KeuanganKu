<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service kecil untuk mengirim notifikasi ke Telegram lewat bot BotFather.
 * Semua kegagalan pengiriman ditangkap agar tidak pernah menggagalkan
 * proses utama (mencatat pendapatan, mengubah wallet, dll).
 */
class TelegramService
{
    /**
     * Kirim pesan mentah ke sebuah chat_id tertentu.
     */
    public function send(?string $chatId, string $message): bool
    {
        $token = config('telegram.bot_token');

        if (blank($chatId) || blank($token)) {
            return false;
        }

        try {
            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if (! $response->successful()) {
                Log::warning('Gagal mengirim notifikasi Telegram', [
                    'chat_id' => $chatId,
                    'response' => $response->body(),
                ]);
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Exception saat mengirim notifikasi Telegram: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Kirim pesan ke user tertentu berdasarkan pengaturan Telegram miliknya.
     * Tidak melakukan apa-apa (dan tidak melempar error) jika user belum
     * mengaktifkan notifikasi Telegram.
     */
    public function notifyUser(User $user, string $message): bool
    {
        $setting = $user->telegramSetting;

        if (! $setting || ! $setting->is_active || blank($setting->chat_id)) {
            return false;
        }

        return $this->send($setting->chat_id, $message);
    }
}
