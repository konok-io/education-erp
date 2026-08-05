<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_balances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('balance_code')->unique();
            $table->unsignedBigInteger('account_id');
            $table->string('account_code');
            $table->string('account_name');
            $table->string('entry_type', 10);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('BDT');
            $table->date('balance_date');
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_document')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('balance_code');
            $table->index('account_id');
            $table->index('fiscal_year_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_balances');
    }
};
