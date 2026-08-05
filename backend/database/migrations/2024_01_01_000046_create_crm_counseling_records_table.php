<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_counseling_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('counseling_no', 50)->unique();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('inquiry_id')->nullable()->constrained('crm_inquiries')->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->date('meeting_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('meeting_type')->default('personal'); // personal, phone, video, online
            $table->text('discussion')->nullable();
            $table->json('documents_discussed')->nullable();
            $table->string('outcome')->nullable(); // positive, negative, followup_needed
            $table->text('recommendation')->nullable();
            $table->date('next_meeting_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('lead_id');
            $table->index('counselor_id');
            $table->index('meeting_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_counseling_records');
    }
};
