<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_repository', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('document_code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('document_type', 50);
            $table->foreignId('project_id')->nullable()->constrained('research_projects')->nullOnDelete();
            $table->foreignId('publication_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_hash')->nullable();
            $table->string('access_type', 50)->default('public');
            $table->string('license')->nullable();
            $table->json('metadata')->nullable();
            $table->string('version')->default('1.0');
            $table->string('doi')->nullable()->unique();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('contributor')->nullable();
            $table->date('published_date')->nullable();
            $table->timestamps();
            
            $table->index('document_code');
            $table->index('document_type');
            $table->index('access_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_repository');
    }
};
