<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_inquiries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('inquiry_no', 50)->unique();
            $table->string('student_name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('session')->nullable();
            $table->foreignId('assigned_counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('inquiry_source', ['website', 'phone', 'walkin', 'referral', 'campaign', 'social_media', 'education_fair'])->default('walkin');
            $table->enum('status', ['new', 'contacted', 'followup', 'converted', 'not_interested'])->default('new');
            $table->text('remarks')->nullable();
            $table->text('notes')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->foreignId('converted_lead_id')->nullable()->unique()->constrained('crm_leads')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'inquiry_source']);
            $table->index('assigned_counselor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_inquiries');
    }
};
