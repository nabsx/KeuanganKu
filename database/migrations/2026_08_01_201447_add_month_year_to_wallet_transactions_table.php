<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->tinyInteger('month')->nullable()->after('transaction_date')->comment('1-12, auto-extracted from transaction_date');
            $table->smallInteger('year')->nullable()->after('month')->comment('YYYY, auto-extracted from transaction_date');
            
            // Add index for efficient monthly queries
            $table->index(['user_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'month', 'year']);
            $table->dropColumn(['month', 'year']);
        });
    }
};
