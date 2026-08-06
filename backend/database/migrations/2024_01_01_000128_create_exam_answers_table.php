<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreignId('question_id')->nullable()->constrained('questions')->cascadeOnDelete();
            $table->text('answer')->nullable();
            $table->text('correct_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->decimal('obtained_marks', 8, 2)->default(0);
            $table->decimal('negative_marks', 8, 2)->default(0);
            $table->integer('time_taken')->default(0);
            $table->enum('status', ['answered', 'not_answered', 'marked_review'])->default('not_answered');
            $table->timestamps();

            $table->unique(['exam_id', 'student_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
