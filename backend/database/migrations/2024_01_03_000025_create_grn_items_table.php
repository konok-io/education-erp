<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('grn_id')->nullable()->constrained('goods_received_notes')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->string('product_name')->nullable();
            $table->decimal('ordered_quantity', 12, 3)->default(0);
            $table->decimal('received_quantity', 12, 3)->default(0);
            $table->decimal('accepted_quantity', 12, 3)->default(0);
            $table->decimal('rejected_quantity', 12, 3)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['grn_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grn_items');
    }
};
