<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_transfers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transfer_no', 50)->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('from_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('to_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('from_designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->foreignId('to_designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->foreignId('from_campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('to_campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('from_shift_id')->nullable()->constrained('employee_shifts')->nullOnDelete();
            $table->foreignId('to_shift_id')->nullable()->constrained('employee_shifts')->nullOnDelete();
            $table->foreignId('reporting_manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('transfer_date');
            $table->date('effective_date');
            $table->enum('transfer_type', [
                'department',
                'campus',
                'designation',
                'shift',
                'reporting_manager',
                'combined'
            ])->default('department');
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', [
                'pending',
                'recommended',
                'approved',
                'cancelled'
            ])->default('pending');
            $table->foreignId('recommended_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('recommended_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('approved_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
            $table->index('transfer_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_transfers');
    }
};
