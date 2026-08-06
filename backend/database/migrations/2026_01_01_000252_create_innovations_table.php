<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('innovations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('innovation_code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('innovation_type', 50)->nullable();
            $table->foreignId('project_id')->nullable()->constrained('research_projects')->nullOnDelete();
            $table->string('stage', 50)->default('prototype');
            $table->text('technology_details')->nullable();
            $table->text('market_potential')->nullable();
            $table->boolean('has_patent')->default(false);
            $table->string('patent_number')->nullable();
            $table->string('trademark')->nullable();
            $table->string('prototype_url')->nullable();
            $table->string('demo_video')->nullable();
            $table->string('status', 50)->default('in_development');
            $table->json('team_members')->nullable();
            $table->decimal('funding_required', 14, 2)->nullable();
            $table->string('funding_currency', 10)->default('USD');
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
            
            $table->index('innovation_code');
            $table->index('innovation_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('innovations');
    }
};
