<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('patent_number')->unique();
            $table->string('patent_title');
            $table->text('abstract')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('research_projects')->nullOnDelete();
            $table->string('patent_type', 50)->nullable();
            $table->string('status', 50)->default('pending');
            $table->string('country')->nullable();
            $table->date('application_date')->nullable();
            $table->date('publication_date')->nullable();
            $table->date('grant_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->json('inventors')->nullable();
            $table->string('applicant')->nullable();
            $table->string('assignee')->nullable();
            $table->string('application_number')->nullable();
            $table->string('publication_number')->nullable();
            $table->string('ip_office')->nullable();
            $table->text('claims')->nullable();
            $table->string('patent_document')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('cost_currency', 10)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('patent_number');
            $table->index('project_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patents');
    }
};
