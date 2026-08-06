<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reconciliation_code')->unique();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->date('statement_date');
            $table->date('reconciliation_date');
            $table->decimal('bank_statement_balance', 15, 2)->default(0);
            $table->decimal('erp_balance', 15, 2)->default(0);
            $table->decimal('deposits_in_transit', 15, 2)->default(0);
            $table->decimal('outstanding_cheques', 15, 2)->default(0);
            $table->decimal('bank_errors', 15, 2)->default(0);
            $table->decimal('erp_errors', 15, 2)->default(0);
            $table->decimal('adjusted_bank_balance', 15, 2)->default(0);
            $table->decimal('adjusted_erp_balance', 15, 2)->default(0);
            $table->string('status', 50)->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('reconciliation_code');
            $table->index('bank_account_id');
            $table->index('statement_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
