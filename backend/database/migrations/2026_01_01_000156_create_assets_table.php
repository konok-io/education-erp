<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('asset_code')->unique();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('asset_name');
            $table->string('serial_number')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->string('category', 100)->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->string('assigned_to_type')->nullable();
            $table->unsignedBigInteger('assigned_to_id')->nullable();
            $table->string('assigned_to_name')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 14, 2)->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('supplier')->nullable();
            $table->string('location')->nullable();
            $table->string('condition', 50)->default('good');
            $table->string('status', 50)->default('available');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            $table->decimal('current_value', 14, 2)->nullable();
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_value', 14, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('asset_code');
            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
