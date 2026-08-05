<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alumni_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('applicant_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('resume')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('university')->nullable();
            $table->string('degree')->nullable();
            $table->string('current_year')->nullable();
            $table->text('skills')->nullable();
            $table->string('status', 50)->default('applied');
            $table->text('employer_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index('internship_id');
            $table->index('alumni_profile_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_applications');
    }
};
