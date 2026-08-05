<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_processing', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('payroll_code')->unique();
            $table->string('payroll_month');
            $table->integer('year');
            $table->integer('month');
            $table->date('processing_date');
            $table->date('payment_date')->nullable();
            $table->decimal('total_gross', 15, 2)->default(0);
            $table->decimal('total_allowance', 15, 2)->default(0);
            $table->decimal('total_deduction', 15, 2)->default(0);
            $table->decimal('total_bonus', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_pf', 15, 2)->default(0);
            $table->decimal('total_net', 15, 2)->default(0);
            $table->integer('total_employees')->default(0);
            $table->integer('paid_employees')->default(0);
            $table->string('status', 50)->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            
            $table->index('payroll_code');
            $table->index('year');
            $table->index('month');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_processing');
    }
};
