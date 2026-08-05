<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_passes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('pass_no')->unique();
            $table->string('passable_type', 100);
            $table->unsignedBigInteger('passable_id');
            $table->string('pass_type', 50)->default('leave');
            $table->foreignId('hostel_id')->nullable()->constrained()->nullOnDelete();
            $table->date('issue_date');
            $table->date('valid_from');
            $table->date('valid_until');
            $table->time('exit_time')->nullable();
            $table->time('return_time')->nullable();
            $table->text('destination')->nullable();
            $table->text('reason')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 50)->default('pending');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('pass_no');
            $table->index(['passable_type', 'passable_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_passes');
    }
};
