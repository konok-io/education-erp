<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_fine_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->enum('member_type', ['student', 'teacher', 'staff', 'researcher', 'guest', 'alumni', 'all'])->default('all');
            $table->enum('fine_type', ['per_day', 'per_week', 'flat_rate', 'percentage'])->default('per_day');
            $table->decimal('amount', 10, 2);
            $table->integer('max_days')->default(0);
            $table->decimal('max_fine', 10, 2)->default(0);
            $table->integer('grace_period')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_fine_rules');
    }
};
