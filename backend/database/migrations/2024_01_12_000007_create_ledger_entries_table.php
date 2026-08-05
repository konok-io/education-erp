<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('ledger_number')->nullable();
            $table->string('voucher_number')->nullable();
            $table->string('voucher_type')->nullable();
            $table->date('transaction_date');
            $table->unsignedBigInteger('account_id');
            $table->string('account_code');
            $table->string('account_name');
            $table->string('entry_type', 10);
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('running_balance', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->unsignedBigInteger('cost_center_id')->nullable();
            $table->string('department')->nullable();
            $table->string('project')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('account_id');
            $table->index('transaction_date');
            $table->index('voucher_number');
            $table->index('journal_entry_id');
            $table->index('fiscal_year_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
