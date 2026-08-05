<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('interview_no', 50)->unique();
            $table->foreignId('job_circular_id')->constrained('job_circulars')->cascadeOnDelete();
            $table->foreignId('job_application_id')->constrained('job_applications')->cascadeOnDelete();
            $table->date('interview_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->string('interview_type')->default('personal'); // personal, panel, written, practical
            $table->json('panel_members')->nullable();
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->decimal('obtained_marks', 8, 2)->nullable();
            $table->text('questions')->nullable();
            $table->text('answers')->nullable();
            $table->text('remarks')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('decision', [
                'pending',
                'selected',
                'rejected',
                'waiting_list',
                'hold'
            ])->default('pending');
            $table->decimal('rating', 3, 2)->nullable();
            $table->json('evaluation_scores')->nullable();
            // Education, Experience, Technical, Communication, Leadership, Overall
            $table->boolean('offer_extended')->default(false);
            $table->date('offer_date')->nullable();
            $table->date('joining_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_circular_id', 'decision']);
            $table->index('interview_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
