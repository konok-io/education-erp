<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('requisition_id')->constrained('purchase_requisitions')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->string('item_name');
            $table->string('specifications')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit', 50)->default('pcs');
            $table->decimal('estimated_price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
};
