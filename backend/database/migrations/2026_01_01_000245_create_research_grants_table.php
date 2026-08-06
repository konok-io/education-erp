<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_grants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('grant_number')->unique();
            $table->unsignedBigInteger('project_id')->nullable()->comment('FK to research_projects.id if needed');
            $table->string('grant_title');
            $table->text('description')->nullable();
            $table->foreignId('funding_agency_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('grant_amount', 14, 2);
            $table->string('currency', 10)->default('USD');
            $table->date('application_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 50)->default('pending');
            $table->json('budget_breakdown')->nullable();
            $table->decimal('released_amount', 14, 2)->default(0);
            $table->text('terms_conditions')->nullable();
            $table->string('agreement_document')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('grant_number');
            $table->index('project_id');
            $table->index('funding_agency_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_grants');
    }
};
