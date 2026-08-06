<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_number')->unique();
            $table->string('transaction_type', 50);
            $table->date('transaction_date');
            $table->string('reference_number')->nullable();
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('description')->nullable();
            $table->decimal('deposit', 15, 2)->default(0);
            $table->decimal('withdrawal', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('bank_balance', 15, 2)->nullable();
            $table->string('status', 50)->default('cleared');
            $table->boolean('is_reconciled')->default(false);
            $table->date('reconciliation_date')->nullable();
            $table->string('payee')->nullable();
            $table->string('payer')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('transaction_number');
            $table->index('bank_account_id');
            $table->index('transaction_date');
            $table->index('is_reconciled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
