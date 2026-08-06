<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receive_note_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('goods_receive_note_id')->constrained('goods_receive_notes')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_description');
            $table->integer('ordered_quantity');
            $table->integer('received_quantity');
            $table->integer('accepted_quantity');
            $table->integer('rejected_quantity')->default(0);
            $table->string('rejection_reason')->nullable();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('condition', 50)->default('good');
            $table->timestamps();
            
            $table->index('goods_receive_note_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receive_note_items');
    }
};
