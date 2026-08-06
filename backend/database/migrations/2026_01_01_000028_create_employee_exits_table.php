<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_exits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('exit_no')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('exit_type', ['resignation', 'termination', 'retirement', 'death'])->default('resignation');
            $table->date('notice_date')->nullable();
            $table->date('last_working_date');
            $table->decimal('salary_amount', 12, 2)->default(0);
            $table->decimal('bonus_amount', 12, 2)->default(0);
            $table->decimal('leave_encashment', 12, 2)->default(0);
            $table->decimal('pf_balance', 12, 2)->default(0);
            $table->decimal('gratuity', 12, 2)->default(0);
            $table->decimal('tax_deduction', 12, 2)->default(0);
            $table->decimal('loan_adjustment', 12, 2)->default(0);
            $table->decimal('advance_adjustment', 12, 2)->default(0);
            $table->decimal('other_deduction', 12, 2)->default(0);
            $table->decimal('net_payable', 12, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'processed', 'completed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('exit_no');
            $table->index('exit_type');
            $table->index('status');
            $table->index(['employee_id', 'last_working_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_exits');
    }
};
