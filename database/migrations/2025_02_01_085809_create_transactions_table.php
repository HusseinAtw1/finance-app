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
            $table->string('name');
            $table->string('phone_number');
            $table->unique(['name', 'user_id']);
            $table->timestampsTz();
        });

        Schema::create('customers', function (Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('phone_number');
            $table->timestampsTz();
            $table->unique(['name', 'phone_number']);
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->decimal('total', 15, 6)->nullable()->unsigned();
            $table->text('description')->nullable();
            $table->timestampTz('transaction_date')->nullable();
            $table->timestampsTz();
            $table->index('transaction_date');
            $table->index('user_id');
            $table->index('status');
        });

        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('transactionable_type');
            $table->unsignedBigInteger('transactionable_id');
            $table->foreignId('account_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('supplier_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('current_price', 15, 6)->unsigned();
            $table->decimal('purchase_price', 15, 6)->unsigned();
            $table->decimal('sold_for', 15, 6)->nullable()->unsigned();
            $table->integer('quantity')->unsigned();
            $table->decimal('amount', 15, 6)->unsigned();
            $table->timestampsTz();
            $table->index('transaction_id');
            $table->index(['transactionable_id', 'transactionable_type'], 'transactable_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('suppliers');
    }
};
