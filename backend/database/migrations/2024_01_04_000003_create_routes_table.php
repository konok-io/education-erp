<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('route_code', 50)->unique();
            $table->string('route_name');
            $table->string('starting_point');
            $table->string('ending_point');
            $table->decimal('distance', 8, 2)->nullable();
            $table->string('estimated_time')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status', 50)->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('route_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
