<?php

declare(strict_types=1);

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
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->string('item_name');
            $table->string('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('ordered_quantity', 12, 2)->default(0);
            $table->decimal('received_quantity', 12, 2)->default(0);
            $table->decimal('rejected_quantity', 12, 2)->default(0);
            $table->string('unit', 50)->default('pcs');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
