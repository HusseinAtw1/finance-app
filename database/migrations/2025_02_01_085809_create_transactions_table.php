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
        Schema::create('suppliers', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->unique();
            $table->timestampsTz();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('total', 15, 6);
            $table->text('description')->nullable();
            $table->timestampTz('transaction_date')->nullable();
            $table->timestampsTz();
            $table->index(['user_id', 'status']);
            $table->index('transaction_date');
            $table->index('user_id');
            $table->index('account_id');
            $table->index('supplier_id');
        });

        Schema::create('transaction_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->morphs('transactionable');
            $table->integer('quantity')->unsigned();
            $table->decimal('amount', 15, 6);
            $table->decimal('purchase_price', 15, 6);
            $table->decimal('current_price', 15, 6);
            $table->timestampsTz();
            $table->index('transaction_id');
            $table->index(['transactionable_id', 'transactionable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_info');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('buyers');
    }
};
