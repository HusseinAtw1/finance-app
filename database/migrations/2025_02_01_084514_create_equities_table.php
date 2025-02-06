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
        Schema::create('equities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('symbol')->index(); // Stock ticker symbol
            $table->decimal('purchase_price', 20, 8); // More precision
            $table->decimal('current_price', 20, 8)->nullable(); // Market value
            $table->integer('quantity');
            $table->decimal('amount', 20, 8); // Total value
            $table->string('currency', 3)->default('USD'); // Default currency
            $table->string('sector')->nullable(); // Stock sector
            $table->decimal('dividends_received', 20, 8)->default(0); // Dividends
            $table->enum('transaction_type', ['buy', 'sell']);
            $table->timestamp('purchased_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equities');
    }
};
