<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_allocations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->string('allocation_code')->unique();
            $table->string('account_code');
            $table->string('account_name');
            $table->string('category', 100)->nullable();
            $table->decimal('allocated_amount', 15, 2)->default(0);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->string('period', 50)->nullable();
            $table->string('status', 50)->default('allocated');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('budget_id');
            $table->index('account_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_allocations');
    }
};
