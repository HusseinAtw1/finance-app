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
        Schema::create('liabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('currency_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('currency_exchange_rate', 15, 6)->unsigned()->nullable();
            $table->string('reference_number');
            $table->string('name');
            $table->decimal('paid_amount', 15, 2)->unsigned()->default(0);
            $table->decimal('total_toBePaid', 15, 2)->unsigned();
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending')->index();
            $table->text('description')->nullable();
            $table->date('due_date')->nullable()->index();
            $table->timestampTz('paid_at')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['user_id', 'name', 'reference_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liabilities');
    }
};

