<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('increments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('increment_no')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('increment_type', ['annual', 'performance', 'promotion', 'manual'])->default('annual');
            $table->decimal('previous_basic', 12, 2);
            $table->decimal('new_basic', 12, 2);
            $table->decimal('increment_amount', 12, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->date('effective_date');
            $table->foreignId('previous_grade_id')->nullable()->constrained('salary_grades')->nullOnDelete();
            $table->foreignId('new_grade_id')->nullable()->constrained('salary_grades')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'active', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('increment_no');
            $table->index('increment_type');
            $table->index('status');
            $table->index(['employee_id', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('increments');
    }
};
