<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_received_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items')->nullOnDelete();
            $table->string('product_name');
            $table->decimal('ordered_quantity', 12, 2)->default(0);
            $table->decimal('received_quantity', 12, 2);
            $table->decimal('accepted_quantity', 12, 2);
            $table->decimal('rejected_quantity', 12, 2)->default(0);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total', 14, 2);
            $table->string('condition', 50)->default('good');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('goods_received_note_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_note_items');
    }
};
