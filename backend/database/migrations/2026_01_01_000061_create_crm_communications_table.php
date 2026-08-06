<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_communications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('communication_no', 50)->unique();
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreignId('campaign_id')->nullable()->constrained('crm_campaigns')->nullOnDelete();
            $table->enum('channel', ['email', 'sms', 'whatsapp', 'push', 'phone'])->default('email');
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
            $table->enum('type', [
                'transactional',
                'promotional',
                'notification',
                'reminder',
                'campaign',
                'autoresponse',
                'broadcast',
            ])->default('transactional');
            $table->string('subject')->nullable();
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->json('attachments')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_mobile')->nullable();
            $table->enum('delivery_status', [
                'queued',
                'sending',
                'sent',
                'delivered',
                'read',
                'failed',
                'bounced',
                'undelivered',
            ])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('related_ticket_id')->nullable()->constrained('crm_tickets')->nullOnDelete();
            $table->timestamps();

            $table->index(['channel', 'delivery_status']);
            $table->index('contact_id');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_communications');
    }
};
