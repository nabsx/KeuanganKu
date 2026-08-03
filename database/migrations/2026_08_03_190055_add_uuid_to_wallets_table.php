<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * IDOR Security: Menambahkan kolom UUID untuk Route Model Binding
     * 
     * Strategi Aman:
     * - Tetap mempertahankan Primary Key (id) sebagai integer
     * - UUID hanya digunakan untuk URL dan Route Model Binding
     * - Foreign Key relationships tetap menggunakan id integer
     * - Proses: Tambah kolom nullable -> Isi UUID untuk data existing -> Ubah jadi unique & NOT NULL
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Step 1: Tambahkan kolom uuid sebagai nullable terlebih dahulu
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Step 2: Generate UUID untuk data existing (production-safe)
        // Menggunakan raw SQL untuk performance yang lebih baik
        DB::table('wallets')->whereNull('uuid')->update([
            'uuid' => DB::raw("UUID()"),
        ]);

        // Step 3: Ubah kolom uuid menjadi unique dan NOT NULL
        Schema::table('wallets', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
