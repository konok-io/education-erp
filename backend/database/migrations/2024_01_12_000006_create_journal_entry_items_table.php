<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entry_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('account_id');
            $table->string('account_code');
            $table->string('account_name');
            $table->string('entry_type', 10);
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->unsignedBigInteger('cost_center_id')->nullable();
            $table->string('department')->nullable();
            $table->string('project')->nullable();
            $table->timestamps();
            
            $table->index('journal_entry_id');
            $table->index('account_id');
            $table->index('entry_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_items');
    }
};
