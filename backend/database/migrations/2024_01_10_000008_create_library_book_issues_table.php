<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_book_issues', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('issue_number')->unique();
            $table->foreignId('inventory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->integer('renewal_count')->default(0);
            $table->integer('max_renewals')->default(2);
            $table->string('status', 50)->default('issued');
            $table->string('issued_by')->nullable();
            $table->string('received_by')->nullable();
            $table->text('condition_on_issue')->nullable();
            $table->text('condition_on_return')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('issue_number');
            $table->index('inventory_id');
            $table->index('book_id');
            $table->index('member_id');
            $table->index('status');
            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_book_issues');
    }
};
