<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_fines', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('fine_number')->unique();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->unsignedBigInteger('issue_id')->nullable();
            $table->string('fine_type', 50);
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('status', 50)->default('pending');
            $table->text('reason')->nullable();
            $table->date('fine_date');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('collected_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('fine_number');
            $table->index('member_id');
            $table->index('fine_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_fines');
    }
};
