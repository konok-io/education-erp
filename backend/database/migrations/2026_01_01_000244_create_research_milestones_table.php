<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_milestones', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('project_id')->constrained('research_projects')->cascadeOnDelete();
            $table->string('milestone_name');
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->string('status', 50)->default('pending');
            $table->integer('progress_percentage')->default(0);
            $table->text('deliverables')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('project_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_milestones');
    }
};
