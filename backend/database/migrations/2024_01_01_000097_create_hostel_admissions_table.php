<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_admissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('admission_no', 50)->unique();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('student_name');
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->foreignId('building_id')->nullable()->constrained('hostel_buildings')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('hostel_rooms')->nullOnDelete();
            $table->foreignId('bed_id')->nullable()->constrained('hostel_beds')->nullOnDelete();
            $table->date('admission_date');
            $table->date('checkout_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'checked_in', 'checked_out', 'rejected', 'cancelled'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
            $table->index(['status', 'admission_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_admissions');
    }
};
