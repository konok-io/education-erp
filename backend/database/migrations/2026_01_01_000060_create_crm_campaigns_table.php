<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('campaign_no', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('campaign_type', [
                'admission',
                'marketing',
                'awareness',
                'event',
                'scholarship',
                'reengagement',
            ]);
            $table->enum('channel', ['email', 'sms', 'whatsapp', 'push', 'multi'])->default('email');
            $table->enum('status', [
                'draft',
                'scheduled',
                'running',
                'paused',
                'completed',
                'cancelled',
            ])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->text('target_audience')->nullable();
            $table->json('audience_filters')->nullable();
            $table->json('template_data')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('responded_count')->default(0);
            $table->integer('converted_count')->default(0);
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('cost_per_send', 10, 2)->nullable();
            $table->decimal('conversion_rate', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'campaign_type']);
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_campaigns');
    }
};
