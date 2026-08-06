<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_notes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('grn_no', 50)->unique();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('challan_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('received_date');
            $table->date('challan_date')->nullable();
            $table->date('invoice_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('condition', ['good', 'damaged', 'partial'])->default('good');
            $table->enum('status', ['pending', 'checked', 'approved', 'rejected'])->default('pending');
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['received_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_notes');
    }
};
