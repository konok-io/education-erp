<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_issues', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('issue_no', 50)->unique();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->foreignId('book_copy_id')->constrained('library_book_copies')->cascadeOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('issue_date')->default(now());
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->foreignId('returned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['issued', 'returned', 'overdue', 'lost', 'renewed'])->default('issued');
            $table->integer('renewal_count')->default(0);
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->decimal('fine_paid', 10, 2)->default(0);
            $table->string('fine_status', 20)->default('pending');
            $table->enum('return_condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'status']);
            $table->index(['book_copy_id', 'status']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_issues');
    }
};
