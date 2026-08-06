<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_seals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('seal_name');
            $table->string('seal_code', 50)->unique();
            $table->string('institution_name');
            $table->text('seal_image');
            $table->string('seal_type', 50)->default('official');
            $table->string('encryption_key')->nullable();
            $table->text('metadata')->nullable();
            $table->string('status', 50)->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('seal_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_seals');
    }
};
