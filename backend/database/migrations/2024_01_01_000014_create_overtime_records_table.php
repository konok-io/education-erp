<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtime_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('overtime_date');
            $table->decimal('hours', 5, 2);
            $table->decimal('rate', 4, 2)->default(1.5);
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('overtime_type', ['normal', 'weekend', 'holiday', 'night'])->default('normal');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'processed', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('overtime_date');
            $table->index('status');
            $table->index(['employee_id', 'overtime_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_records');
    }
};
