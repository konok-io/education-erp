<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_agencies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('agency_code')->unique();
            $table->string('agency_name');
            $table->string('agency_type', 50);
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('eligibility_criteria')->nullable();
            $table->text('funding_types')->nullable();
            $table->timestamps();
            
            $table->index('agency_code');
            $table->index('agency_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_agencies');
    }
};
