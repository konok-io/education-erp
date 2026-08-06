<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receive_note_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('grn_id')->constrained('goods_receive_notes')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->string('item_name');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('accepted_quantity', 12, 2)->default(0);
            $table->decimal('rejected_quantity', 12, 2)->default(0);
            $table->string('unit', 50)->default('pcs');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receive_note_items');
    }
};
