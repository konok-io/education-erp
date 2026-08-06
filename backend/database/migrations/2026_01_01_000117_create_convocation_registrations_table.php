<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convocation_registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('registration_no', 50)->unique();
            $table->foreignId('convocation_id')->nullable()->constrained('convocations')->cascadeOnDelete();
            $table->foreignId('alumni_id')->nullable()->constrained('alumni_members')->nullOnDelete();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('roll_number', 50)->nullable();
            $table->string('registration_no_old', 50)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('program', 100)->nullable();
            $table->year('passing_year')->nullable();
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('payment_status', 50)->default('unpaid');
            $table->string('transaction_id')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('guest_name', 150)->nullable();
            $table->string('guest_relation', 50)->nullable();
            $table->integer('total_guests')->default(0);
            $table->text('dietary_requirements')->nullable();
            $table->text('accessibility_needs')->nullable();
            $table->string('certificate_path')->nullable();
            $table->string('seat_number', 20)->nullable();
            $table->enum('attendance', ['registered', 'attended', 'absent'])->default('registered');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['convocation_id', 'alumni_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convocation_registrations');
    }
};
