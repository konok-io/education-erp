<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbt_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('session_token', 255)->unique();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('total_time')->default(0);
            $table->integer('remaining_time')->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'expired', 'terminated'])->default('not_started');
            $table->integer('tab_switches')->default(0);
            $table->boolean('fullscreen_active')->default(false);
            $table->json('answered_questions')->nullable();
            $table->json('flags')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_sessions');
    }
};
