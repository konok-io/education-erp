<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->uuid('student_uuid')->unique();
            $table->string('student_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->nullOnDelete();
            $table->foreignId('academic_level_id')->nullable()->constrained('academic_levels')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->string('birth_place')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->date('admission_date');
            $table->enum('status', ['pending', 'active', 'inactive', 'graduated', 'transferred', 'expelled'])->default('active');
            $table->enum('student_type', ['regular', 'distance', 'evening'])->default('regular');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('student_id');
            $table->index('status');
            $table->index('admission_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
