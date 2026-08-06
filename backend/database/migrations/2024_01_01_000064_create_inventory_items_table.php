<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('item_code', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->cascadeOnDelete();
            $table->string('unit', 50)->default('pcs');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('min_stock_level', 12, 2)->default(0);
            $table->decimal('max_stock_level', 12, 2)->default(0);
            $table->decimal('reorder_level', 12, 2)->default(0);
            $table->decimal('opening_stock', 12, 2)->default(0);
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->boolean('is_serialized')->default(false);
            $table->boolean('is_taxable')->default(true);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->string('image')->nullable();
            $table->json('specifications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index('is_serialized');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
