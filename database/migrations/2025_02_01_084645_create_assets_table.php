<?php

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

        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name'); // e.g., 'cash', 'property', 'stock', 'bond'
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['user_id', 'name']);
        });

        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name'); // 'category', ['fixed', 'liquid', 'semi_liquid']
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['user_id', 'name']);
        });

        Schema::create('asset_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name'); // e.g., 'active', 'pending', 'sold', 'inactive', 'archived', 'suspended', etc.
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['user_id', 'name']);
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('asset_type_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('asset_category_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('asset_status_id')->constrained('asset_statuses')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->decimal('current_value', 15, 2)->unsigned();
            $table->decimal('purchase_price', 15, 2)->unsigned()->nullable();
            $table->decimal('sell_price', 15, 2)->unsigned()->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestampTz('sell_at')->nullable();
            $table->timestampTz('purchase_at')->useCurrent();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['user_id', 'name']);
            $table->index('user_id');
            $table->index('account_id');
            $table->index('currency_id');
            $table->index('asset_type_id');
        });

        Schema::create('asset_value_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->decimal('value', 15, 2)->unsigned();
            $table->timestampTz('recorded_at')->useCurrent();
            $table->timestampsTz();

            $table->index('asset_id'); // Single index
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_value_history');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_statuses');
        Schema::dropIfExists('asset_categories');
        Schema::dropIfExists('asset_types');
    }

};
