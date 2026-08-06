<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('loan_code')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('loan_type', 50);
            $table->string('loan_name');
            $table->decimal('principal_amount', 12, 2)->default(0);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('interest_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->integer('tenure_months')->default(0);
            $table->decimal('monthly_installment', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);
            $table->integer('paid_installments')->default(0);
            $table->integer('remaining_installments')->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('disbursement_date')->nullable();
            $table->string('status', 50)->default('pending');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('loan_code');
            $table->index('employee_id');
            $table->index('loan_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_loans');
    }
};
