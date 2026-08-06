<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('template_code', 50)->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->enum('certificate_type', ['testimonial', 'character', 'transfer', 'experience', 'bonafide', 'graduation', 'transcript', 'other'])->default('other');
            $table->text('template_content');
            $table->json('variables')->nullable();
            $table->string('background_image')->nullable();
            $table->string('logo')->nullable();
            $table->string('signature')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
