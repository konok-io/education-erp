<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('job_number')->unique();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete();
            $table->string('job_title');
            $table->text('description')->nullable();
            $table->string('job_type', 50);
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('work_type', 50)->default('full_time');
            $table->integer('vacancy')->default(1);
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            $table->string('experience_required', 50)->nullable();
            $table->string('education_required')->nullable();
            $table->string('skills_required')->nullable();
            $table->decimal('min_salary', 12, 2)->nullable();
            $table->decimal('max_salary', 12, 2)->nullable();
            $table->string('salary_currency', 10)->default('USD');
            $table->string('salary_frequency', 50)->default('yearly');
            $table->date('application_deadline')->nullable();
            $table->date('start_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status', 50)->default('open');
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index('job_number');
            $table->index('job_type');
            $table->index('work_type');
            $table->index('is_featured');
            $table->index('status');
            $table->index('application_deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
