<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('proposal_number')->unique();
            $table->foreignId('project_id')->nullable()->constrained('research_projects')->nullOnDelete();
            $table->string('proposal_title');
            $table->text('proposal_abstract')->nullable();
            $table->string('proposal_type', 50)->nullable();
            $table->string('current_stage', 50)->default('draft');
            $table->string('status', 50)->default('draft');
            $table->text('department_comments')->nullable();
            $table->text('committee_comments')->nullable();
            $table->text('ethics_comments')->nullable();
            $table->text('principal_comments')->nullable();
            $table->string('department_approved_by')->nullable();
            $table->timestamp('department_approved_at')->nullable();
            $table->string('committee_approved_by')->nullable();
            $table->timestamp('committee_approved_at')->nullable();
            $table->string('ethics_approved_by')->nullable();
            $table->timestamp('ethics_approved_at')->nullable();
            $table->string('principal_approved_by')->nullable();
            $table->timestamp('principal_approved_at')->nullable();
            $table->string('proposal_document')->nullable();
            $table->string('budget_document')->nullable();
            $table->string('ethics_certificate')->nullable();
            $table->string('approval_document')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('proposal_number');
            $table->index('project_id');
            $table->index('status');
            $table->index('current_stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_submissions');
    }
};
