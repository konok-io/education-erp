<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_event_registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('registration_no', 50)->unique();
            $table->foreignId('event_id')->nullable()->constrained('alumni_events')->cascadeOnDelete();
            $table->foreignId('alumni_id')->nullable()->constrained('alumni_members')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('organization', 200)->nullable();
            $table->string('designation', 150)->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('payment_status', 50)->default('unpaid');
            $table->string('transaction_id')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('attendance', ['registered', 'attended', 'absent'])->default('registered');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->unique(['event_id', 'alumni_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_event_registrations');
    }
};
