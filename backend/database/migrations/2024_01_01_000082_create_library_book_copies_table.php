<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_book_copies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('accession_no', 50)->unique();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->string('copy_number', 20)->nullable();
            $table->string('barcode', 100)->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->foreignId('shelf_id')->nullable()->constrained('library_shelves')->nullOnDelete();
            $table->foreignId('rack_id')->nullable()->constrained('library_racks')->nullOnDelete();
            $table->enum('condition', ['new', 'good', 'fair', 'poor', 'damaged'])->default('new');
            $table->enum('status', ['available', 'issued', 'reserved', 'lost', 'damaged', 'repair', 'archived'])->default('available');
            $table->date('purchase_date')->nullable();
            $table->date('last_issue_date')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'status']);
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_book_copies');
    }
};
