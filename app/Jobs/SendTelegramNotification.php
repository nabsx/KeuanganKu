<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk mengirim notifikasi Telegram secara asynchronous.
 *
 * Usage:
 *   SendTelegramNotification::dispatch($user, $message);
 */
class SendTelegramNotification implements ShouldQueue
{
    use Queueable;

    /** Jumlah maksimal percobaan sebelum job dianggap gagal. */
    public int $tries = 3;

    /** Timeout maksimal untuk job ini (dalam detik). */
    public int $timeout = 30;

    public function __construct(
        private User $user,
        private string $message,
        private ?string $customBotToken = null,
    ) {
        // Kalau butuh queue/delay khusus, set di sini via method, BUKAN properti:
        // $this->onQueue('telegram');
        // $this->delay(now()->addSeconds(5));
    }

    public function handle(TelegramService $telegramService): void
    {
        Log::info('Processing SendTelegramNotification job', [
            'user_id' => $this->user->id,
            'message_preview' => substr($this->message, 0, 50),
        ]);

        $success = $this->customBotToken
            ? $telegramService->send($this->user->telegramSetting?->chat_id, $this->message, $this->customBotToken)
            : $telegramService->notifyUser($this->user, $this->message);

        if ($success) {
            Log::info('Telegram notification sent successfully', [
                'user_id' => $this->user->id,
            ]);
        } else {
            Log::warning('Failed to send Telegram notification', [
                'user_id' => $this->user->id,
                'attempt' => $this->attempts(),
            ]);
        }
    }

    /**
     * Backoff strategy: retry ke-1 tunggu 1s, ke-2 tunggu 5s, ke-3 tunggu 10s.
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendTelegramNotification job failed after all retries', [
            'user_id' => $this->user->id,
            'exception' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }

    // middleware() dihapus dulu — tambahkan lagi setelah RateLimiter::for()
    // didaftarkan di AppServiceProvider (lihat Opsi A di atas)
}