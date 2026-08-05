<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('ticket_no', 50)->unique();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->enum('category', [
                'admission',
                'accounts',
                'result',
                'attendance',
                'routine',
                'library',
                'hostel',
                'transport',
                'technical',
                'general',
            ]);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent', 'critical'])->default('medium');
            $table->enum('status', [
                'open',
                'assigned',
                'in_progress',
                'waiting',
                'resolved',
                'closed',
                'cancelled',
            ])->default('open');
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->json('cc')->nullable();
            $table->json('attachments')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('parent_ticket_id')->nullable()->constrained('crm_tickets')->nullOnDelete();
            $table->foreignId('related_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('related_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->integer('response_count')->default(0);
            $table->integer('resolution_time_hours')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'status']);
            $table->index(['priority', 'status']);
            $table->index('assigned_to');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_tickets');
    }
};
