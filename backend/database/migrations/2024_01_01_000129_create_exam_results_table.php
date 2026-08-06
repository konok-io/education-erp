<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('result_no', 50)->unique();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('student_name')->nullable();
            $table->string('roll_number', 50)->nullable();
            $table->decimal('obtained_marks', 8, 2)->default(0);
            $table->decimal('full_marks', 8, 2)->default(100);
            $table->decimal('pass_marks', 8, 2)->default(33);
            $table->decimal('percentage', 8, 2)->default(0);
            $table->enum('status', ['pending', 'evaluated', 'verified', 'approved', 'published'])->default('pending');
            $table->enum('result', ['passed', 'failed', 'absent', 'disqualified'])->nullable();
            $table->integer('total_correct')->default(0);
            $table->integer('total_wrong')->default(0);
            $table->integer('total_not_answered')->default(0);
            $table->decimal('negative_marks', 8, 2)->default(0);
            $table->text('teacher_remarks')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['exam_id', 'status']);
            $table->index(['student_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
