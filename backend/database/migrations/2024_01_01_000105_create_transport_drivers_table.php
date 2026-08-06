<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_drivers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('driver_id', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('father_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('license_no', 50)->unique();
            $table->string('license_type', 50)->nullable();
            $table->date('license_expiry')->nullable();
            $table->integer('experience_years')->default(0);
            $table->date('joining_date');
            $table->decimal('salary', 10, 2)->default(0);
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'on_leave', 'suspended', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_drivers');
    }
};
