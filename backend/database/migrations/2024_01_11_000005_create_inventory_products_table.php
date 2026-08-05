<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('inventory_brands')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('inventory_units')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->nullable();
            $table->integer('current_stock')->default(0);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('average_cost', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('image')->nullable();
            $table->json('specifications')->nullable();
            $table->boolean('is_trackable')->default(true);
            $table->boolean('is_serialized')->default(false);
            $table->boolean('has_expiry')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('sku');
            $table->index('barcode');
            $table->index('category_id');
            $table->index('warehouse_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};
