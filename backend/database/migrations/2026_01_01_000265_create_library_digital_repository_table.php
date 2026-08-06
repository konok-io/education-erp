<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_digital_repository', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('document_code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('document_type', 50);
            $table->foreignId('category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_hash')->nullable();
            $table->string('access_type', 50)->default('public');
            $table->string('license')->nullable();
            $table->string('doi')->nullable()->unique();
            $table->string('contributor')->nullable();
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('document_code');
            $table->index('document_type');
            $table->index('access_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_digital_repository');
    }
};
