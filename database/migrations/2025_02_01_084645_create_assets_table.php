<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('type')->comment('Type of the investment e.g(cash, property)');
            $table->decimal('current_value', 15, 2)->unsigned();
            $table->decimal('purchase_price', 15, 2)->unsigned()->nullable();
            $table->decimal('sell_price', 15, 2)->unsigned()->nullable();
            $table->enum('category', ['fixed', 'liquid', 'semi_liquid']);
            $table->enum('status', ['active', 'pending', 'sold', 'inactive'])->default('active');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestampTz('sell_at')->nullable();
            $table->timestampTz('purchase_at')->default()->useCurrent();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['user_id', 'name']);
            $table->index('user_id');
            $table->index('account_id');
            $table->index('currency_id'); // rate this schema 1 to 10 1 is lowes 10 is highest and suggest changes if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
