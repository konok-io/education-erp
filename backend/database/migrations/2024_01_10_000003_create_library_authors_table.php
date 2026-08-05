<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_authors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('author_code')->unique();
            $table->string('author_name');
            $table->text('biography')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('country')->nullable();
            $table->string('photo')->nullable();
            $table->text('specialization')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('author_code');
            $table->index('author_name');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_authors');
    }
};
