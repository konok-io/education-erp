<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('name_bn')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('product_brands')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('product_units')->nullOnDelete();
            $table->string('model')->nullable();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->string('image')->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->decimal('min_stock', 12, 2)->default(0);
            $table->decimal('max_stock', 12, 2)->nullable();
            $table->decimal('reorder_level', 12, 2)->nullable();
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->string('weight')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->boolean('is_trackable')->default(true);
            $table->boolean('is_sellable')->default(true);
            $table->boolean('is_purchasable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('sku');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
