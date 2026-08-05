<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('exam_subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_roll')->nullable();
            $table->decimal('theory_marks', 8, 2)->nullable();
            $table->decimal('practical_marks', 8, 2)->nullable();
            $table->decimal('total_marks', 8, 2)->nullable();
            $table->decimal('pass_marks', 8, 2)->nullable();
            $table->string('result', 20)->nullable();
            $table->string('grade', 5)->nullable();
            $table->text('teacher_remarks')->nullable();
            $table->text('moderator_remarks')->nullable();
            $table->string('status', 50)->default('draft');
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('entered_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['exam_subject_id', 'student_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};
