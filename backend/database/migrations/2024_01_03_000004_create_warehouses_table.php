<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('type', 50)->default('main');
            $table->text('address')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
