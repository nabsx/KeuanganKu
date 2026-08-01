<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telegram_settings', function (Blueprint $table) {
            // Tambah kolom bot_token yang nullable (opsional)
            // Jika null, akan menggunakan TELEGRAM_BOT_TOKEN dari .env
            $table->text('bot_token')->nullable()->after('chat_id');
        });
    }

    public function down(): void
    {
        Schema::table('telegram_settings', function (Blueprint $table) {
            $table->dropColumn('bot_token');
        });
    }
};
