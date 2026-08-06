<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('conference_name');
            $table->string('conference_code')->unique();
            $table->string('organizer')->nullable();
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('has_proceedings')->default(false);
            $table->string('issn')->nullable();
            $table->boolean('is_indexed')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('conference_code');
            $table->index('country');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
