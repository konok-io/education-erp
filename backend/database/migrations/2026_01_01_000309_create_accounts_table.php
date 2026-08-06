<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('account_code')->unique();
            $table->string('account_name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('account_type', 50);
            $table->string('account_group', 100);
            $table->string('account_nature', 20)->default('debit');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('debit_total', 15, 2)->default(0);
            $table->decimal('credit_total', 15, 2)->default(0);
            $table->string('currency', 10)->default('BDT');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_bank')->default(false);
            $table->boolean('is_cash')->default(false);
            $table->boolean('allow_manual_entry')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            
            $table->index('account_code');
            $table->index('parent_id');
            $table->index('account_type');
            $table->index('account_group');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
