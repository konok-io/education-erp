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
            $table->string('fine_no')->unique();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->foreignId('issue_id')->nullable()->constrained('book_issues')->nullOnDelete();
            $table->string('fine_type', 50);
            $table->string('reason');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('waived_amount', 10, 2)->default(0);
            $table->date('fine_date');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('status', 50)->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->string('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('fine_no');
            $table->index('member_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_fines');
    }
};
