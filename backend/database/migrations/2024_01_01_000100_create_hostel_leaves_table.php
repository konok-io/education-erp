<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_leaves', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('leave_no', 50)->unique();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('building_id')->nullable()->constrained('hostel_buildings')->nullOnDelete();
            $table->date('leave_date');
            $table->date('return_date');
            $table->text('reason');
            $table->string('destination')->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('approval_remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_leaves');
    }
};
