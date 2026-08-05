<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('thesis_number')->unique();
            $table->string('thesis_title');
            $table->text('abstract')->nullable();
            $table->string('thesis_type', 50)->default('thesis');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name');
            $table->string('student_email')->nullable();
            $table->string('student_roll')->nullable();
            $table->string('department')->nullable();
            $table->string('program')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('co_supervisor')->nullable();
            $table->string('degree')->nullable();
            $table->year('submission_year');
            $table->date('submission_date')->nullable();
            $table->date('defense_date')->nullable();
            $table->decimal('defense_score', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->json('committee_members')->nullable();
            $table->json('keywords')->nullable();
            $table->string('status', 50)->default('submitted');
            $table->string('pdf_document')->nullable();
            $table->string('doi')->nullable()->unique();
            $table->text('acknowledgments')->nullable();
            $table->text('references')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('thesis_number');
            $table->index('student_id');
            $table->index('department');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
