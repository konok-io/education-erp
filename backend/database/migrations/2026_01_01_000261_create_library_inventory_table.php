<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_inventory', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('inventory_code')->unique();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('barcode')->nullable()->unique();
            $table->string('rfid')->nullable()->unique();
            $table->string('status', 50)->default('available');
            $table->string('condition', 50)->default('good');
            $table->string('current_location')->nullable();
            $table->unsignedBigInteger('holder_id')->nullable();
            $table->string('holder_type')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('last_check_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('inventory_code');
            $table->index('book_id');
            $table->index('barcode');
            $table->index('rfid');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_inventory');
    }
};
