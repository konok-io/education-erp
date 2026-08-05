<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('conversation_no', 50)->unique();
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->string('visitor_ip')->nullable();
            $table->enum('source', ['website', 'student_portal', 'admin_dashboard', 'mobile_app'])->default('website');
            $table->enum('status', ['waiting', 'active', 'closed'])->default('waiting');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('closing_note')->nullable();
            $table->integer('message_count')->default(0);
            $table->integer('duration_minutes')->nullable();
            $table->enum('rating', ['1', '2', '3', '4', '5'])->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index(['status', 'source']);
            $table->index('assigned_agent_id');
        });

        Schema::create('crm_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('conversation_id')->constrained('crm_chat_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('sender_type', ['agent', 'visitor', 'system'])->default('visitor');
            $table->text('message');
            $table->enum('message_type', ['text', 'image', 'file', 'system'])->default('text');
            $table->json('attachments')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_chat_messages');
        Schema::dropIfExists('crm_chat_conversations');
    }
};
