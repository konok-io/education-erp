<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('question_code', 50)->unique();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('question_categories')->nullOnDelete();
            $table->string('chapter', 100)->nullable();
            $table->string('topic', 100)->nullable();
            $table->enum('question_type', ['mcq', 'cq', 'written', 'short', 'true_false', 'fill_blank', 'matching', 'programming', 'math', 'diagram'])->default('mcq');
            $table->enum('difficulty', ['easy', 'medium', 'hard', 'expert'])->default('medium');
            $table->decimal('marks', 8, 2)->default(1);
            $table->text('question');
            $table->text('question_bn')->nullable();
            $table->json('options')->nullable();
            $table->text('correct_answer');
            $table->text('explanation')->nullable();
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('usage_count')->default(0);
            $table->decimal('success_rate', 5, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'inactive', 'pending_review'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subject_id', 'difficulty']);
            $table->index(['question_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
