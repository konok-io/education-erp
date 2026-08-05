<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_communication_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('template_code', 50)->unique();
            $table->enum('channel', ['email', 'sms', 'whatsapp'])->default('email');
            $table->enum('category', [
                'admission',
                'fee_reminder',
                'result',
                'welcome',
                'password_reset',
                'certificate',
                'general_notice',
                'holiday',
                'event',
            ])->default('general_notice');
            $table->string('subject')->nullable();
            $table->text('content');
            $table->text('content_bd')->nullable();
            $table->json('variables')->nullable(); // [name, email, etc.]
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['channel', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_communication_templates');
    }
};
