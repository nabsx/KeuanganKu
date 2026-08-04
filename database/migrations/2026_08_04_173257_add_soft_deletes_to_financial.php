<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wallets', fn (Blueprint $table) => $table->softDeletes());
        Schema::table('wallet_transactions', fn (Blueprint $table) => $table->softDeletes());
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('wallets', fn (Blueprint $table) => $table->dropSoftDeletes());
    }
};

// This file intentionally uses Laravel's standard SoftDeletes columns for recoverability.
