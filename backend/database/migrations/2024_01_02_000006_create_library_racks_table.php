<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_racks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('shelf_id')->constrained('library_shelves')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('row', 10)->nullable();
            $table->string('column', 10)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('shelf_id');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_racks');
    }
};
