<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('code', 50)->unique();
            $table->string('country', 100)->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_brands');
    }
};
