<?php

declare(strict_types=1);

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
            $table->string('book_code', 50)->unique();
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('subtitle')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('language', 50)->default('English');
            $table->integer('pages')->default(0);
            $table->year('publication_year')->nullable();
            $table->string('edition', 50)->nullable();
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('replacement_cost', 10, 2)->default(0);
            $table->text('keywords')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->boolean('is_digital')->default(false);
            $table->string('digital_file')->nullable();
            $table->integer('digital_size')->nullable();
            $table->enum('digital_access', ['public', 'students', 'teachers', 'staff', 'all'])->default('all');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index('isbn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};
