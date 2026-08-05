<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_announcements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('announcement_no', 50)->unique();
            $table->string('title');
            $table->text('content');
            $table->enum('announcement_type', [
                'general',
                'academic',
                'exam',
                'holiday',
                'event',
                'emergency',
                'administrative',
            ])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->date('publish_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('show_on_website')->default(true);
            $table->boolean('show_on_portal')->default(true);
            $table->boolean('send_notification')->default(false);
            $table->json('target_audience')->nullable(); // student, guardian, teacher, staff, all
            $table->json('attachments')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'announcement_type']);
            $table->index('publish_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_announcements');
    }
};
