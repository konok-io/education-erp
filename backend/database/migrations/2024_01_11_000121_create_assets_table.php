<?php

declare(strict_types=1);

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
            $table->string('asset_code', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->foreignId('category_id')->constrained('asset_categories')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->enum('condition', ['new', 'good', 'fair', 'poor'])->default('new');
            $table->enum('status', ['available', 'assigned', 'maintenance', 'repair', 'lost', 'disposed'])->default('available');
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('current_value', 12, 2)->default(0);
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->date('depreciation_start_date')->nullable();
            $table->decimal('salvage_value', 12, 2)->default(0);
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('inventory_locations')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_insurable')->default(false);
            $table->string('insurance_policy_no')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index(['status', 'condition']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
