<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('loan_no')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('loan_type', ['personal', 'house', 'vehicle', 'emergency', 'festival'])->default('personal');
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('total_interest', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('monthly_installment', 12, 2);
            $table->unsignedSmallInteger('installment_count');
            $table->unsignedSmallInteger('paid_installments')->default(0);
            $table->decimal('remaining_amount', 12, 2);
            $table->date('loan_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'approved', 'active', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('loan_no');
            $table->index('status');
            $table->index('loan_type');
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
