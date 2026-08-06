<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_verifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('certificate_number')->nullable();
            $table->string('verification_token')->nullable();
            $table->string('verifier_name')->nullable();
            $table->string('verifier_email')->nullable();
            $table->string('verifier_ip', 45)->nullable();
            $table->string('verification_method', 50)->default('qr');
            $table->timestamp('verified_at')->nullable();
            $table->string('status', 50)->default('success');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('certificate_number');
            $table->index('verification_token');
            $table->index('verified_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_verifications');
    }
};
