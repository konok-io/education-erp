<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_oncall_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('service_id')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('rotation_type'); // daily, weekly, custom
            $table->json('rotation_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('set null');
            $table->index('name');
        });

        Schema::create('observability_oncall_shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('schedule_id');
            $table->uuid('user_id');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->boolean('is_override')->default(false);
            $table->uuid('override_by_user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('schedule_id')->references('id')->on('observability_oncall_schedules')->onDelete('cascade');
            $table->index(['schedule_id', 'start_time', 'end_time']);
            $table->index('user_id');
        });

        Schema::create('observability_status_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('status')->default('operational'); // operational, degraded, partial_outage, major_outage, maintenance
            $table->json('header_settings')->nullable();
            $table->json('footer_settings')->nullable();
            $table->json('custom_css')->nullable();
            $table->boolean('show_incident_history')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('status');
        });

        Schema::create('observability_status_page_components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('status_page_id');
            $table->uuid('service_id')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('position')->nullable();
            $table->string('status'); // operational, degraded, partial_outage, major_outage, maintenance, unknown
            $table->boolean('show_history')->default(true);
            $table->timestamps();

            $table->foreign('status_page_id')->references('id')->on('observability_status_pages')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('set null');
            $table->index(['status_page_id', 'position']);
        });

        Schema::create('observability_status_page_incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('status_page_id');
            $table->uuid('incident_id');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->foreign('status_page_id')->references('id')->on('observability_status_pages')->onDelete('cascade');
            $table->foreign('incident_id')->references('id')->on('observability_incidents')->onDelete('cascade');
            $table->index('status_page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_status_page_incidents');
        Schema::dropIfExists('observability_status_page_components');
        Schema::dropIfExists('observability_status_pages');
        Schema::dropIfExists('observability_oncall_shifts');
        Schema::dropIfExists('observability_oncall_schedules');
    }
};
