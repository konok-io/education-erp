<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_continuity_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('status'); // draft, active, suspended, archived
            $table->string('scope'); // organization, department, system, process
            $table->json('critical_systems')->nullable();
            $table->json('recovery_priorities')->nullable();
            $table->unsignedInteger('rto_minutes')->default(60);
            $table->unsignedInteger('rpo_minutes')->default(15);
            $table->json('emergency_contacts')->nullable();
            $table->json('escalation_matrix')->nullable();
            $table->json('communication_plan')->nullable();
            $table->json('recovery_procedures')->nullable();
            $table->json('resource_requirements')->nullable();
            $table->json('roles_responsibilities')->nullable();
            $table->text('testing_frequency')->nullable();
            $table->timestampTz('last_tested_at')->nullable();
            $table->timestampTz('next_test_at')->nullable();
            $table->json('metadata')->nullable();
            $table->uuid('tenant_id')->nullable();
            $table->uuid('owner_id')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['scope', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_continuity_plans');
    }
};
