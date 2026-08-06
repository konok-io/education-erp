<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 50)->default('academic'); // academic, transport, hostel, exam, other
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('frequency', 50)->default('monthly'); // one-time, monthly, quarterly, yearly
            $table->boolean('is_optional')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};
