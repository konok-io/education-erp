<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('book_code')->unique();
            $table->string('isbn')->nullable()->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('edition')->nullable();
            $table->string('language', 50)->default('English');
            $table->string('book_type', 50)->default('physical');
            $table->foreignId('category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('library_authors')->nullOnDelete();
            $table->foreignId('publisher_id')->nullable()->constrained('library_publishers')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('library_branches')->nullOnDelete();
            $table->year('publication_year')->nullable();
            $table->integer('pages')->nullable();
            $table->string('shelf')->nullable();
            $table->string('rack')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->json('keywords')->nullable();
            $table->json('subjects')->nullable();
            $table->string('edition_year')->nullable();
            $table->string('volume')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->boolean('is_digital')->default(false);
            $table->string('digital_file')->nullable();
            $table->string('digital_format')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_reference_only')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('book_code');
            $table->index('isbn');
            $table->index('category_id');
            $table->index('author_id');
            $table->index('publisher_id');
            $table->index('book_type');
            $table->index('is_digital');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};
