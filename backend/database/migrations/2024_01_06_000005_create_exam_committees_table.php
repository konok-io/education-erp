<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_committees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('committee_name');
            $table->string('committee_code', 50)->unique();
            $table->foreignId('exam_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('chairman_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('controller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('responsibilities')->nullable();
            $table->text('description')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->string('status', 50)->default('active');
            $table->timestamps();
            
            $table->index('committee_code');
            $table->index('exam_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_committees');
    }
};
