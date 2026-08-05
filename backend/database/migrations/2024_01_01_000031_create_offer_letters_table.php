<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_letters', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('offer_no', 50)->unique();
            $table->foreignId('job_circular_id')->constrained('job_circulars')->cascadeOnDelete();
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->foreignId('interview_id')->nullable()->constrained('interviews')->nullOnDelete();
            $table->string('candidate_name');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->foreignId('designation_id')->constrained('designations')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('employment_type_id')->constrained('employment_types')->cascadeOnDelete();
            $table->foreignId('salary_grade_id')->nullable()->constrained('salary_grades')->nullOnDelete();
            $table->decimal('offered_salary', 12, 2)->nullable();
            $table->date('offer_date');
            $table->date('joining_date');
            $table->text('terms_conditions')->nullable();
            $table->text('benefits')->nullable();
            $table->enum('status', [
                'draft',
                'sent',
                'accepted',
                'declined',
                'expired',
                'joined'
            ])->default('draft');
            $table->date('response_date')->nullable();
            $table->text('response_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'offer_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_letters');
    }
};
