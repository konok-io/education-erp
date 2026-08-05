<?php

declare(strict_types=1);

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
            $table->string('application_no', 50)->unique();
            $table->foreignId('job_circular_id')->constrained('job_circulars')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->string('marital_status')->nullable();
            $table->string('nid', 50)->nullable();
            $table->string('passport', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('alternative_mobile', 20)->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('photo')->nullable();
            $table->string('cv')->nullable();
            $table->string('cover_letter')->nullable();
            $table->json('certificates')->nullable();
            $table->text('experience_details')->nullable();
            $table->text('education_details')->nullable();
            $table->enum('applicant_status', [
                'applied',
                'under_review',
                'shortlisted',
                'interview_scheduled',
                'interviewed',
                'selected',
                'rejected',
                'waiting_list',
                'withdrawn'
            ])->default('applied');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_circular_id', 'applicant_status']);
            $table->index('email');
            $table->index('mobile');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
