<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duplicate_certificate_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('request_number')->unique();
            $table->string('certificate_type', 50);
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_roll')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('father_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('reason')->nullable();
            $table->text('description')->nullable();
            $table->string('police_clearance')->nullable();
            $table->string('newspaper_ad')->nullable();
            $table->decimal('fee_amount', 10, 2)->nullable();
            $table->string('payment_status', 50)->default('pending');
            $table->string('status', 50)->default('pending');
            $table->text('admin_remarks')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index('request_number');
            $table->index('student_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duplicate_certificate_requests');
    }
};
