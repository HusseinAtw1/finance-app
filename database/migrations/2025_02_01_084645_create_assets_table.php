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
            $table->string('name')->unique(); // e.g., 'active', 'pending', 'sold', 'inactive', 'archived', 'suspended', etc.
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        DB::table('asset_statuses')->insert([
            ['name' => 'Active',   'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pending',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sold',     'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inactive', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Archived', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Suspended','created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->string('method')->unique();
            $table->timestampsTz();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('currency_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('currency_exchange_rate', 15, 6)->unsigned()->nullable();
            $table->foreignId('asset_type_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('asset_category_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('asset_status_id')->constrained('asset_statuses')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('asset_depreciation_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->integer('quantity')->unsigned()->nullable();
            $table->decimal('current_value', 15, 6)->nullable()->unsigned();
            $table->decimal('purchase_price', 15, 6)->unsigned()->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestampTz('purchase_at')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['name', 'reference_number']);
            $table->index('user_id');
            $table->index('currency_id');
            $table->index('asset_type_id');
        });

        Schema::create('asset_value_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->decimal('value', 15, 2)->unsigned();
            $table->timestampTz('recorded_at')->useCurrent();
            $table->timestampsTz();
            $table->index('asset_id');
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
