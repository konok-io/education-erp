<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convocations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('convocation_no', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->year('year');
            $table->string('semester', 50)->nullable();
            $table->date('ceremony_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue', 200);
            $table->string('address')->nullable();
            $table->string('chief_guest', 150)->nullable();
            $table->string('special_guest', 200)->nullable();
            $table->string('guest_speaker', 200)->nullable();
            $table->text('agenda')->nullable();
            $table->integer('expected_attendees')->default(0);
            $table->integer('registered_attendees')->default(0);
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['planning', 'registration', 'confirmed', 'completed', 'cancelled'])->default('planning');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convocations');
    }
};
