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
            $table->string('bonus_code')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('bonus_type', 50);
            $table->string('bonus_name');
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('basic_salary', 12, 2)->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->date('effective_date');
            $table->text('reason')->nullable();
            $table->string('status', 50)->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('bonus_code');
            $table->index('employee_id');
            $table->index('bonus_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
