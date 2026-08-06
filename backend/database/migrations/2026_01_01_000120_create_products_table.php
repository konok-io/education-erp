<?php

declare(strict_types=1);

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
            $table->string('product_code', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('product_units')->nullOnDelete();
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('mrp', 12, 2)->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->integer('max_stock_level')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->enum('product_type', ['standard', 'service', 'combo', 'digital'])->default('standard');
            $table->enum('tax_type', ['inclusive', 'exclusive'])->default('exclusive');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index(['sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
