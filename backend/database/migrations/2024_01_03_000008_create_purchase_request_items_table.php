<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('specifications')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->foreignId('unit_id')->nullable()->constrained('product_units')->nullOnDelete();
            $table->decimal('estimated_rate', 12, 2)->nullable();
            $table->decimal('estimated_amount', 14, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
            
            $table->index('purchase_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_items');
    }
};
