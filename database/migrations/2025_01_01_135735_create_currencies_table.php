<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name', 3);
            $table->string('full_name');
            $table->string('symbol')->nullable();
            $table->decimal('exchange_rate', 15, 6);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'name']);
        });

        Schema::create('exchange_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('old_exchange_rate', 15, 6);
            $table->decimal('new_exchange_rate', 15, 6);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_histories');
        Schema::dropIfExists('currencies');
    }
};
