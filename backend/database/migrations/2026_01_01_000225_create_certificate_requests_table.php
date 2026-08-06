<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('request_no', 50)->unique();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_email')->nullable();
            $table->string('student_phone', 20)->nullable();
            $table->enum('certificate_type', [
                'character', 'transfer', 'testimonial', 'bonafide', 
                'experience', 'course_completion', 'training', 'internship',
                'migration', 'scholarship', 'achievement', 'other'
            ])->default('other');
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('payment_status', 50)->default('unpaid');
            $table->string('transaction_id')->nullable();
            $table->date('payment_date')->nullable();
            $table->foreignId('certificate_id')->nullable()->constrained('certificates')->nullOnDelete();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'generated', 'issued', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
            $table->index(['certificate_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_requests');
    }
};
