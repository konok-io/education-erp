<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alumni_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('applicant_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('resume')->nullable();
            $table->string('cover_letter')->nullable();
            $table->string('portfolio_link')->nullable();
            $table->string('linkedin')->nullable();
            $table->text('experience_summary')->nullable();
            $table->json('skills')->nullable();
            $table->string('expected_salary', 50)->nullable();
            $table->string('current_company')->nullable();
            $table->string('current_designation')->nullable();
            $table->string('status', 50)->default('applied');
            $table->text('employer_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index('job_id');
            $table->index('alumni_profile_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
