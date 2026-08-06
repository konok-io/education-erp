<?php

declare(strict_types=1);

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
            $table->string('transcript_no', 50)->unique();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('student_name')->nullable();
            $table->string('roll_number', 50)->nullable();
            $table->string('registration_no', 50)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('program', 100)->nullable();
            $table->year('admission_year')->nullable();
            $table->year('passing_year')->nullable();
            $table->string('degree', 100)->nullable();
            $table->decimal('total_credits', 8, 2)->default(0);
            $table->decimal('cgpa', 5, 2)->nullable();
            $table->decimal('scale', 5, 2)->default(4.0);
            $table->text('result_summary')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['draft', 'verified', 'approved', 'issued', 'cancelled'])->default('draft');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcripts');
    }
};
