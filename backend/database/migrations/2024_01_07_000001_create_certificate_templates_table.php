<?php

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
            $table->string('template_name');
            $table->string('template_code', 50)->unique();
            $table->string('certificate_type', 50);
            $table->text('template_content')->nullable();
            $table->json('template_config')->nullable();
            $table->string('background_image')->nullable();
            $table->string('header_logo')->nullable();
            $table->string('footer_image')->nullable();
            $table->string('digital_seal')->nullable();
            $table->string('signature_positions')->nullable();
            $table->string('qr_position')->nullable();
            $table->string('barcode_position')->nullable();
            $table->text('css_styles')->nullable();
            $table->string('status', 50)->default('active');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('template_code');
            $table->index('certificate_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
