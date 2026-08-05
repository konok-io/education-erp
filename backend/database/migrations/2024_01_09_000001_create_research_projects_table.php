<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_projects', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('project_code')->unique();
            $table->string('project_title');
            $table->text('abstract')->nullable();
            $table->text('objectives')->nullable();
            $table->text('expected_outcome')->nullable();
            $table->string('category')->nullable();
            $table->string('research_type', 50);
            $table->string('department')->nullable();
            $table->json('keywords')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 50)->default('draft');
            $table->string('priority', 50)->default('normal');
            $table->decimal('budget', 14, 2)->nullable();
            $table->string('budget_currency', 10)->default('USD');
            $table->text('methodology')->nullable();
            $table->text('literature_review')->nullable();
            $table->text('scope')->nullable();
            $table->text('limitations')->nullable();
            $table->text('references')->nullable();
            $table->string('ethics_approval')->nullable();
            $table->string('ethics_certificate')->nullable();
            $table->foreignId('principal_investigator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('proposal_document')->nullable();
            $table->string('progress_report')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('project_code');
            $table->index('category');
            $table->index('research_type');
            $table->index('status');
            $table->index('principal_investigator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_projects');
    }
};
