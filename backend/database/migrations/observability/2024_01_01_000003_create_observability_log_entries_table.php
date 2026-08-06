<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_log_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->string('level'); // debug, info, warning, error, critical
            $table->string('source'); // laravel, react, electron, android, ios, system
            $table->text('message');
            $table->json('context')->nullable();
            $table->json('extra')->nullable();
            $table->string('environment')->default('production');
            $table->string('host')->nullable();
            $table->string('trace_id')->nullable();
            $table->string('span_id')->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('observability_services')->onDelete('set null');
            $table->index(['level', 'logged_at']);
            $table->index(['source', 'logged_at']);
            $table->index(['service_id', 'logged_at']);
            $table->index('trace_id');
            $table->index('logged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_log_entries');
    }
};
