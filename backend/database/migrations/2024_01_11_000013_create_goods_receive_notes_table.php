<?php

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
            $table->string('grn_number')->unique();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->date('received_date');
            $table->string('delivery_note_number')->nullable();
            $table->integer('total_items')->default(0);
            $table->integer('accepted_items')->default(0);
            $table->integer('rejected_items')->default(0);
            $table->string('status', 50)->default('pending');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('condition_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('grn_number');
            $table->index('purchase_order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receive_notes');
    }
};
