<?php

declare(strict_types=1);

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
            $table->string('fine_no', 50)->unique();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->foreignId('issue_id')->nullable()->constrained('library_issues')->nullOnDelete();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('fine_type', ['overdue', 'lost', 'damaged', 'late_return'])->default('overdue');
            $table->string('reason');
            $table->integer('days')->default(0);
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'waived'])->default('pending');
            $table->date('issue_date')->default(now());
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_fines');
    }
};
