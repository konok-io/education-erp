<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('promotion_no')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('previous_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('new_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('previous_designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->foreignId('new_designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->foreignId('previous_grade_id')->nullable()->constrained('salary_grades')->nullOnDelete();
            $table->foreignId('new_grade_id')->nullable()->constrained('salary_grades')->nullOnDelete();
            $table->decimal('previous_basic', 12, 2)->nullable();
            $table->decimal('new_basic', 12, 2)->nullable();
            $table->date('promotion_date');
            $table->date('effective_date');
            $table->enum('status', ['pending', 'approved', 'active', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('promotion_no');
            $table->index('status');
            $table->index(['employee_id', 'promotion_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
