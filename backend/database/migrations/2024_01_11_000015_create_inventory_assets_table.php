<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('asset_code')->unique();
            $table->string('asset_tag')->unique();
            $table->string('asset_name');
            $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->string('brand')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->text('description')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('current_value', 12, 2)->nullable();
            $table->decimal('salvage_value', 12, 2)->default(0);
            $table->integer('useful_life_years')->nullable();
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            $table->string('depreciation_method', 50)->default('straight_line');
            $table->decimal('accumulated_depreciation', 12, 2)->default(0);
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('room')->nullable();
            $table->string('assigned_to')->nullable();
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->string('assigned_type')->nullable();
            $table->date('assigned_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('insurance_policy')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->string('condition', 50)->default('good');
            $table->string('status', 50)->default('available');
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->string('image')->nullable();
            $table->json('specifications')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('asset_code');
            $table->index('asset_tag');
            $table->index('category_id');
            $table->index('status');
            $table->index('assigned_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_assets');
    }
};
