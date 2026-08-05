<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('signature_name');
            $table->string('signatory_name');
            $table->string('designation');
            $table->string('department')->nullable();
            $table->text('signature_image');
            $table->string('signature_type', 50)->default('image');
            $table->string('digital_certificate')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('metadata')->nullable();
            $table->string('status', 50)->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('signatory_name');
            $table->index('designation');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};
