<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('audit_code')->unique();
            $table->string('audit_type', 50);
            $table->string('audit_name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 50)->default('planned');
            $table->string('scope')->nullable();
            $table->text('objective')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->string('risk_level', 50)->nullable();
            $table->enum('compliance_status', ['compliant', 'non_compliant', 'partial', 'not_applicable'])->nullable();
            $table->foreignId('auditor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('audit_code');
            $table->index('audit_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
