<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('inventory_locations')->nullOnDelete();
            $table->enum('movement_type', [
                'purchase',
                'sale',
                'transfer_in',
                'transfer_out',
                'adjustment_in',
                'adjustment_out',
                'return_in',
                'return_out',
                'damaged',
                'expired'
            ]);
            $table->decimal('quantity', 12, 2);
            $table->decimal('stock_before', 12, 2)->default(0);
            $table->decimal('stock_after', 12, 2)->default(0);
            $table->string('reference_type')->nullable(); // PurchaseOrder, Invoice, GRN, etc.
            $table->string('reference_no')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'movement_type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
