<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alumni_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('registrant_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('ticket_type')->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('payment_status', 50)->default('pending');
            $table->string('transaction_id')->nullable();
            $table->boolean('attended')->default(false);
            $table->boolean('certificate_generated')->default(false);
            $table->string('certificate_number')->nullable();
            $table->text('feedback')->nullable();
            $table->string('status', 50)->default('registered');
            $table->timestamps();
            
            $table->index('event_id');
            $table->index('alumni_profile_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
