<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('certificate_no', 50)->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('issue_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_years')->default(0);
            $table->integer('total_months')->default(0);
            $table->text('experience_summary')->nullable();
            $table->text('performance_remarks')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('verification_code')->unique()->nullable();
            $table->string('qr_code')->nullable();
            $table->string('pdf_file')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'issue_date']);
        });

        Schema::create('noc_certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('certificate_no', 50)->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('noc_type'); // general, visa, immigration, employment, government
            $table->date('issue_date');
            $table->text('purpose')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('verification_code')->unique()->nullable();
            $table->string('qr_code')->nullable();
            $table->string('pdf_file')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('authorized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'issue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noc_certificates');
        Schema::dropIfExists('experience_certificates');
    }
};
