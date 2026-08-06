<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('exam_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject_code')->nullable();
            $table->string('subject_name');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->default(180);
            $table->decimal('full_marks', 8, 2);
            $table->decimal('pass_marks', 8, 2);
            $table->decimal('practical_marks', 8, 2)->default(0);
            $table->decimal('theory_marks', 8, 2)->default(0);
            $table->string('exam_mode', 20)->default('written');
            $table->text('syllabus')->nullable();
            $table->string('status', 50)->default('scheduled');
            $table->timestamps();
            
            $table->index('exam_id');
            $table->index('subject_id');
            $table->index('exam_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_subjects');
    }
};
