<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_followups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('followup_no', 50)->unique();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('inquiry_id')->nullable()->constrained('crm_inquiries')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->cascadeOnDelete();
            $table->foreignId('conducted_by')->constrained('users')->cascadeOnDelete();
            $table->enum('followup_type', ['phone_call', 'email', 'whatsapp', 'sms', 'meeting', 'video_call'])->default('phone_call');
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->date('conducted_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'rescheduled', 'cancelled', 'no_response'])->default('pending');
            $table->text('purpose')->nullable();
            $table->text('outcome')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'scheduled_date']);
            $table->index('conducted_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_followups');
    }
};
