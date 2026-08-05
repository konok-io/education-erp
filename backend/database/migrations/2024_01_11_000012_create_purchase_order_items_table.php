<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_description');
            $table->integer('quantity');
            $table->integer('received_quantity')->default(0);
            $table->integer('rejected_quantity')->default(0);
            $table->foreignId('unit_id')->nullable()->constrained('inventory_units')->nullOnDelete();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('specifications')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
