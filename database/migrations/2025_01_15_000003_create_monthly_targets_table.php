<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('month')->comment('1-12');
            $table->smallInteger('year')->comment('YYYY');
            $table->decimal('target_amount', 15, 2)->comment('Target tabungan bulanan');
            $table->timestamps();

            // Ensure one target per user per month
            $table->unique(['user_id', 'month', 'year']);
            $table->index(['user_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_targets');
    }
};
