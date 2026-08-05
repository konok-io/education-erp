<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_slabs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->unsignedSmallInteger('fiscal_year');
            $table->decimal('min_income', 14, 2)->default(0);
            $table->decimal('max_income', 14, 2)->nullable();
            $table->decimal('rate_percent', 5, 2);
            $table->decimal('fixed_amount', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('fiscal_year');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_slabs');
    }
};
