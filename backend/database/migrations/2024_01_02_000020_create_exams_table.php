<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('exam_code', 50)->unique();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->foreignId('session_id')->nullable()->constrained('exam_sessions')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('exam_type', ['class_test', 'quiz', 'assignment', 'mid_term', 'final', 'model_test', 'admission', 'practical', 'viva', 'improvement', 'supplementary'])->default('class_test');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration')->default(60);
            $table->decimal('full_marks', 8, 2)->default(100);
            $table->decimal('pass_marks', 8, 2)->default(33);
            $table->decimal('practical_marks', 8, 2)->default(0);
            $table->decimal('theory_marks', 8, 2)->default(100);
            $table->foreignId('center_id')->nullable()->constrained('exam_centers')->nullOnDelete();
            $table->enum('mode', ['online', 'offline', 'cbt', 'omr'])->default('offline');
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->text('description')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('negative_marking')->default(false);
            $table->decimal('negative_mark_value', 5, 2)->default(0.25);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['session_id', 'status']);
            $table->index(['exam_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
