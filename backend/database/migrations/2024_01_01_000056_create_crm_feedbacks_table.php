<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('feedback_no', 50)->unique();
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('crm_tickets')->nullOnDelete();
            $table->enum('feedback_type', [
                'suggestion',
                'complaint',
                'compliment',
                'service_rating',
                'experience',
            ])->default('suggestion');
            $table->string('subject');
            $table->text('content');
            $table->integer('rating')->nullable(); // 1-5
            $table->json('metadata')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', ['submitted', 'reviewed', 'in_progress', 'resolved', 'closed'])->default('submitted');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['feedback_type', 'status']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_feedbacks');
    }
};
