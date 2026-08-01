<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Bot Telegram
|--------------------------------------------------------------------------
| bot_token didapat dari @BotFather di Telegram setelah membuat bot baru
| dengan perintah /newbot. Chat ID masing-masing user disimpan terpisah
| di tabel telegram_settings (diatur lewat menu "Telegram" di aplikasi).
*/

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
];
