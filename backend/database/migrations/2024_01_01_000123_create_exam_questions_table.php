<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->foreignId('question_id')->nullable()->constrained('questions')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->decimal('marks', 8, 2)->default(1);
            $table->boolean('is_randomized')->default(false);
            $table->timestamps();

            $table->unique(['exam_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
