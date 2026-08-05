<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transcript_number')->unique();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_roll')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('department')->nullable();
            $table->string('program')->nullable();
            $table->string('session')->nullable();
            $table->string('duration')->nullable();
            $table->json('semester_results')->nullable();
            $table->decimal('total_credits', 8, 2)->nullable();
            $table->decimal('cgpa', 5, 2)->nullable();
            $table->decimal('gpa', 5, 2)->nullable();
            $table->string('result_status', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('verification_token')->nullable()->unique();
            $table->text('pdf_path')->nullable();
            $table->foreignId('signature_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('seal_id')->nullable()->constrained()->nullOnDelete();
            $table->date('issue_date')->nullable();
            $table->string('status', 50)->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('transcript_number');
            $table->index('student_id');
            $table->index('verification_token');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcripts');
    }
};
