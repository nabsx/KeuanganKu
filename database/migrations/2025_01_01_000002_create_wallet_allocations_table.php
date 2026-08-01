<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->decimal('percentage', 5, 2)->default(0);
            $table->timestamps();

            $table->unique('wallet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_allocations');
    }
};
