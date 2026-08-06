<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('isbn', 20)->unique()->nullable();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('edition', 50)->nullable();
            $table->string('language', 50)->default('English');
            $table->foreignId('category_id')->nullable()->constrained('book_categories')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->text('description')->nullable();
            $table->integer('publication_year')->nullable();
            $table->integer('pages')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 10)->default('BDT');
            $table->text('keywords')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('pdf_file')->nullable();
            $table->boolean('is_digital')->default(false);
            $table->boolean('is_reference_only')->default(false);
            $table->unsignedSmallInteger('total_copies')->default(1);
            $table->unsignedSmallInteger('available_copies')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('isbn');
            $table->index('category_id');
            $table->index('subject_id');
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
