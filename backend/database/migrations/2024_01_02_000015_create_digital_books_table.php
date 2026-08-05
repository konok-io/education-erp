<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_books', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('book_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('book_categories')->nullOnDelete();
            $table->string('file_type', 20);
            $table->string('file_path');
            $table->string('file_size')->nullable();
            $table->integer('page_count')->nullable();
            $table->string('isbn')->nullable();
            $table->string('author_name')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('publication_year')->nullable();
            $table->string('language', 50)->default('English');
            $table->string('access_type', 50)->default('public');
            $table->string('download_permission', 50)->default('allowed');
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('title');
            $table->index('category_id');
            $table->index('file_type');
            $table->index('access_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_books');
    }
};
