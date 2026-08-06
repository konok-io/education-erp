<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practical_exams', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('exam_code', 50)->unique();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('lab_name', 150);
            $table->string('instructor_name', 150)->nullable();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('practical_marks', 8, 2)->default(0);
            $table->decimal('obtained_marks', 8, 2)->nullable();
            $table->text('observation')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'conducted', 'evaluated'])->default('pending');
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practical_exams');
    }
};
