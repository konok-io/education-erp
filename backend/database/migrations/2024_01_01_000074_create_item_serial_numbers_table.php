<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->string('serial_number')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->enum('status', ['available', 'sold', 'assigned', 'damaged', 'returned'])->default('available');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->date('warranty_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_serial_numbers');
    }
};
