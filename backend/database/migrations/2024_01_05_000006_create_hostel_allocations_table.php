<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_allocations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('allocation_no')->unique();
            $table->string('allocatable_type', 100);
            $table->unsignedBigInteger('allocatable_id');
            $table->foreignId('hostel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('building_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->date('check_in_date')->nullable();
            $table->date('expected_checkout')->nullable();
            $table->date('actual_checkout')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->string('status', 50)->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['allocatable_type', 'allocatable_id']);
            $table->index('allocation_no');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_allocations');
    }
};
