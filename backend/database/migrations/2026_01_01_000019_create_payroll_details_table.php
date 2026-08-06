<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->string('component_type', 50);
            $table->string('component_name', 100);
            $table->decimal('amount', 12, 2)->default(0);
            $table->boolean('is_earning')->default(true);
            $table->timestamps();
            
            $table->index('payroll_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
    }
};
