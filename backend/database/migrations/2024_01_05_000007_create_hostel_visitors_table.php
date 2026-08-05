<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_visitors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('visitor_no')->unique();
            $table->string('visitor_name');
            $table->string('nid', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('relation', 50)->nullable();
            $table->string('purpose', 50)->nullable();
            $table->foreignId('hostel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_name')->nullable();
            $table->string('student_class')->nullable();
            $table->string('student_roll')->nullable();
            $table->date('visit_date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 50)->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('visitor_no');
            $table->index('visit_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_visitors');
    }
};
