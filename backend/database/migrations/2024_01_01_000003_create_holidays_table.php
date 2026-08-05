<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->date('holiday_date');
            $table->enum('holiday_type', ['weekly', 'national', 'religious', 'institution', 'emergency'])->default('institution');
            $table->boolean('is_repeating')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('holiday_date');
            $table->index('holiday_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
