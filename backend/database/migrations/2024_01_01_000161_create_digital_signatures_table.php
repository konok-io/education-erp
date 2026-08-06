<?php

declare(strict_types=1);

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
            $table->string('name');
            $table->string('designation', 150);
            $table->string('department', 100)->nullable();
            $table->string('signature_image')->nullable();
            $table->string('seal_image')->nullable();
            $table->string('certificate_path')->nullable();
            $table->text('certificate_data')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->enum('signature_type', ['principal', 'controller', 'registrar', 'dean', 'hod', 'authorized'])->default('authorized');
            $table->enum('status', ['active', 'inactive', 'expired', 'revoked'])->default('active');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};
