<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_blueprints', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('blueprint_code', 50)->unique();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('question_distribution');
            $table->json('difficulty_distribution');
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->integer('total_questions')->default(50);
            $table->enum('status', ['draft', 'approved', 'active'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subject_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_blueprints');
    }
};
