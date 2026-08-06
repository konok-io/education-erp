<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('lead_no', 50)->unique();
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->string('full_name');
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('lead_source', 50);
            $table->string('course_interested')->nullable();
            $table->string('session')->nullable();
            $table->foreignId('assigned_counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent', 'critical'])->default('medium');
            $table->enum('pipeline_stage', [
                'new',
                'contacted',
                'interested',
                'counseling',
                'application',
                'admission',
                'rejected',
                'lost',
            ])->default('new');
            $table->integer('lead_score')->default(0);
            $table->date('expected_admission_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('last_discussion')->nullable();
            $table->date('last_followup')->nullable();
            $table->date('next_followup')->nullable();
            $table->enum('status', ['active', 'converted', 'rejected', 'lost'])->default('active');
            $table->unsignedBigInteger('converted_to_student_id')->nullable()->unique();
            $table->date('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pipeline_stage', 'status']);
            $table->index('lead_source');
            $table->index('assigned_counselor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};
