<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentorships', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('mentorship_number')->unique();
            $table->foreignId('mentor_id')->nullable()->constrained('alumni_profiles')->nullOnDelete();
            $table->foreignId('mentee_id')->nullable()->constrained('alumni_profiles')->nullOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('mentee_name');
            $table->string('mentee_email');
            $table->string('mentee_phone')->nullable();
            $table->string('expertise_area')->nullable();
            $table->text('goals')->nullable();
            $table->text('background')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('meeting_frequency', 50)->nullable();
            $table->string('status', 50)->default('active');
            $table->text('notes')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('sessions_completed')->default(0);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('mentorship_number');
            $table->index('mentor_id');
            $table->index('mentee_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentorships');
    }
};
