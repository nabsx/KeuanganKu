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
     * Kirim pesan mentah ke sebuah chat_id tertentu dengan bot token yang spesifik.
     * Jika bot_token null, menggunakan default dari config.
     */
    public function send(?string $chatId, string $message, ?string $botToken = null): bool
    {
        // Gunakan bot token yang diberikan, jika tidak ada gunakan dari config
        $token = $botToken ?: config('telegram.bot_token');

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
     * Menggunakan bot_token user jika tersedia, jika tidak gunakan default dari config.
     * Tidak melakukan apa-apa (dan tidak melempar error) jika user belum
     * mengaktifkan notifikasi Telegram.
     */
    public function notifyUser(User $user, string $message): bool
    {
        $setting = $user->telegramSetting;

        if (! $setting || ! $setting->is_active || blank($setting->chat_id)) {
            return false;
        }

        // Gunakan bot_token user jika ada, jika tidak gunakan default
        $botToken = $setting->bot_token ?: config('telegram.bot_token');

        return $this->send($setting->chat_id, $message, $botToken);
    }
}
