<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('placement_number')->unique();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('alumni_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name');
            $table->string('student_email');
            $table->string('company_name');
            $table->string('designation');
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('salary_currency', 10)->default('USD');
            $table->string('employment_type', 50)->default('full_time');
            $table->date('joining_date')->nullable();
            $table->text('offer_letter')->nullable();
            $table->string('status', 50)->default('offer_accepted');
            $table->text('remarks')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('placement_number');
            $table->index('employer_id');
            $table->index('alumni_profile_id');
            $table->index('status');
            $table->index('joining_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};
