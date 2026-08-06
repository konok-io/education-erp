<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rack_id')->nullable()->constrained('library_racks')->nullOnDelete();
            $table->string('accession_number')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->string('qr_code')->unique()->nullable();
            $table->string('condition', 50)->default('good');
            $table->string('status', 50)->default('available');
            $table->date('acquisition_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('accession_number');
            $table->index('barcode');
            $table->index('status');
            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
