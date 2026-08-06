<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receive_notes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('grn_no', 50)->unique();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('received_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->date('received_date');
            $table->enum('status', ['pending', 'partial', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['purchase_order_id', 'status']);
            $table->index('received_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receive_notes');
    }
};
