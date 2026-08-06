<?php

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
            $table->string('certificate_number')->unique();
            $table->string('certificate_type', 50);
            $table->foreignId('template_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_roll')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('department')->nullable();
            $table->string('class_name')->nullable();
            $table->string('section')->nullable();
            $table->string('session')->nullable();
            $table->string('semester')->nullable();
            $table->string('academic_year')->nullable();
            $table->text('content')->nullable();
            $table->json('metadata')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('barcode')->nullable();
            $table->string('verification_token')->nullable()->unique();
            $table->string('digital_hash')->nullable();
            $table->text('pdf_path')->nullable();
            $table->foreignId('signature_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('seal_id')->nullable()->constrained()->nullOnDelete();
            $table->date('issue_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('reason')->nullable();
            $table->text('conduct')->nullable();
            $table->string('status', 50)->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('certificate_number');
            $table->index('certificate_type');
            $table->index('student_id');
            $table->index('verification_token');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
