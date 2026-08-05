<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('bonus_no')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('bonus_type', ['festival', 'performance', 'yearly', 'special'])->default('festival');
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->date('bonus_date');
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('bonus_no');
            $table->index('bonus_type');
            $table->index('status');
            $table->index(['employee_id', 'bonus_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
