<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('code')->unique();
            $table->string('short_code', 5);
            $table->integer('leave_days')->default(0);
            $table->boolean('is_paid')->default(true);
            $table->boolean('is_encashable')->default(false);
            $table->boolean('is_carry_forward')->default(false);
            $table->integer('max_consecutive_days')->default(0);
            $table->integer('max_carry_forward_days')->default(0);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_active');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
