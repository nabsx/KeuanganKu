<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimit;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk mengirim notifikasi Telegram secara asynchronous.
 *
 * Implementasi ShouldQueue membuat job ini di-queue instead of langsung dijalankan.
 * Ini memastikan HTTP request ke Telegram tidak memblok response ke user.
 *
 * Usage:
 *   SendTelegramNotification::dispatch($user, $message);
 */
class SendTelegramNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Jumlah maksimal percobaan sebelum job dianggap gagal.
     * Default: 3 kali
     */
    public int $tries = 3;

    /**
     * Timeout maksimal untuk job ini (dalam detik).
     * Jika melebihi, job akan dihentikan.
     */
    public int $timeout = 30;

    /**
     * Delay sebelum job mulai diproses (dalam detik).
     * Biarkan 0 untuk langsung diproses.
     */
    public int $delay = 0;

    /**
     * Nama queue untuk job ini.
     * Bisa didefinisikan di sini atau saat dispatch.
     */
    public string $queue = 'default';

    /**
     * Constructor - menerima parameter yang dibutuhkan.
     * Parameter ini akan di-serialize dan disimpan di tabel jobs.
     */
    public function __construct(
        private User $user,
        private string $message,
        private ?string $customBotToken = null,
    ) {
    }

    /**
     * Eksekusi job.
     * Method ini dipanggil oleh queue worker.
     */
    public function handle(TelegramService $telegramService): void
    {
        // Log untuk debugging
        Log::info('Processing SendTelegramNotification job', [
            'user_id' => $this->user->id,
            'message_preview' => substr($this->message, 0, 50),
        ]);

        // Kirim notifikasi
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
     * Backoff strategy - menentukan delay antara retry.
     *
     * Contoh: retry ke-1 tunggu 1s, retry ke-2 tunggu 5s, retry ke-3 tunggu 10s
     *
     * @return array
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    /**
     * Dipanggil jika job gagal setelah semua percobaan habis.
     * Method ini bisa digunakan untuk cleanup atau logging.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendTelegramNotification job failed after all retries', [
            'user_id' => $this->user->id,
            'exception' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Bisa tambah logic di sini:
        // - Send email ke user
        // - Update status di database
        // - Alert admin
    }

    /**
     * Middleware untuk job ini (optional).
     * RateLimit mencegah terlalu banyak job running secara bersamaan.
     */
    public function middleware(): array
    {
        return [
            // Limit job ini: max 10 concurrent executions per minute
            new RateLimit('telegram-notifications', 10, 60),
        ];
    }
}
