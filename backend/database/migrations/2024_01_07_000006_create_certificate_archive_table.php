<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_archive', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('document_type', 50);
            $table->string('document_number')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('student_roll')->nullable();
            $table->string('document_category', 100)->nullable();
            $table->string('file_path');
            $table->string('file_type', 50)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->text('file_hash')->nullable();
            $table->string('storage_type', 50)->default('local');
            $table->string('cloud_url')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('version', 20)->default('1.0');
            $table->string('status', 50)->default('active');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('document_type');
            $table->index('student_id');
            $table->index('document_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_archive');
    }
};
