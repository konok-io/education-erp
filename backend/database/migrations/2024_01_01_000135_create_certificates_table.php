<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('certificate_no', 50)->unique();
            $table->string('certificate_code', 50)->nullable();
            $table->foreignId('template_id')->nullable()->constrained('certificate_templates')->nullOnDelete();
            $table->enum('certificate_type', ['testimonial', 'character', 'transfer', 'experience', 'bonafide', 'graduation', 'transcript', 'other'])->default('other');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_name_bn')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('roll_number', 50)->nullable();
            $table->string('registration_no', 50)->nullable();
            $table->string('class', 50)->nullable();
            $table->string('section', 50)->nullable();
            $table->string('group', 50)->nullable();
            $table->year('passing_year')->nullable();
            $table->text('subjects')->nullable();
            $table->text('gpa')->nullable();
            $table->text('grades')->nullable();
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('digital_signature')->nullable();
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['draft', 'approved', 'printed', 'issued', 'cancelled'])->default('draft');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'certificate_type']);
            $table->index(['status', 'issue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
