<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('verification_no', 50)->unique();
            $table->foreignId('certificate_id')->nullable()->constrained('certificates')->nullOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('applicant_name')->nullable();
            $table->string('applicant_email')->nullable();
            $table->string('applicant_phone', 20)->nullable();
            $table->enum('document_type', ['certificate', 'transcript', 'marksheet', 'nid', 'passport', 'other'])->default('certificate');
            $table->string('document_name')->nullable();
            $table->string('document_number', 100)->nullable();
            $table->string('document_path')->nullable();
            $table->date('issue_date')->nullable();
            $table->enum('verification_type', ['self', 'third_party', 'employer', 'institution'])->default('self');
            $table->string('verifier_name', 150)->nullable();
            $table->string('verifier_email')->nullable();
            $table->string('verifier_organization', 200)->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->text('verification_details')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('qr_code')->nullable();
            $table->string('verification_link')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['document_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_verifications');
    }
};
